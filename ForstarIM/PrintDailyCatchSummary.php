<?php
	require("include/include.php");

	#Setting No.of Rows
		$numRows 		= 20; // Default:20
		$proSummaryNumRows	= 30;
		$wtChllanSumryNumRows	= 30;
		$qtySumryNumRows	= 30;
		//$challanSumryNumRows	= 30;

	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");

	# Advance Search
	$advSearch = $g["advSearch"];
	$selectedTableHeader = $sessObj->getValue("selTableHeader");
	$arraySize	= $sessObj->getValue("arraySize");

	$dateFrom = $g["supplyFrom"];
	$dateTill = $g["supplyTill"];

	$selDate	=	$g["selDate"];

	#Date From which challan SCD-> Supplier Challan Date, WCD-> WT challan Date
	$dateSelectFrom = $g["dateSelectFrom"];

 	if ($selDate) {
		$sDate		=	explode("/",$selDate);
		$selectADate	=	$sDate[2]."-".$sDate[1]."-".$sDate[0];
  	 }

	$selectUnit		=	$g["selUnit"];
	$landingCenterId	=	$g["landingCenter"];
	$selectSupplier		=	$g["supplier"];
	$billingCompany		= 	$g["billingCompany"];


	#Finding the Plant Name
	$plantRec		=	$plantandunitObj->find($selectUnit);
	//rekha updated dated on 16 june 2018
	$plantName		=	stripSlash($plantRec[3]);
	//$plantName		=	stripSlash($plantRec[2]);
	#Finding SupplierName
	$supplierRec		=	$supplierMasterObj->find($selectSupplier);
	$supplierName		=	$supplierRec[2];
	#Finding Landing Center Name
	$centerRec		=	$landingcenterObj->find($landingCenterId);
	$landingCenterName	=	stripSlash($centerRec[1]);

	//Rekha updated code here dated on 13 feb 2019 
		$comprec = $billingCompanyObj->find($billingCompany);
		$billcompanyN = $comprec[1];
	//end code 
	
	
	
	
	$fishId				=	$g["fish"];
	$processId			=	$g["processCode"];

	$Date1			=	explode("/",$dateFrom);
	$fromDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];

	$Date2			=	explode("/",$dateTill);
	$tillDate		=	$Date2[2]."-".$Date2[1]."-".$Date2[0];

	if ($selDate) {
		$dateFrom = $selDate;
		$dateTill = $selDate;
	}

	$details	=	$g["details"];
	$proCount	= 	$g["proCount"];
	$proSummary	=	$g["proSummary"];
	$fishCatchSummary 	= $g["fishCatchSummary"];
	$wtChallanSummary 	= $g["wtChallanSummary"];
	$supplierMemo		= $g["supplierMemo"];
	$declWtSummary 	=	$g["declWtSummary"];
	$rateNAmount	=	$g["rateNAmount"];
	$RMMatrix	=	$g["RMMatrix"];
	$localQtyReportChk = $g["localQtyReportChk"];
	$subSupplierChk 	= $g["subSupplierChk"];
	$RMRateMatrix		= $g["RMRateMatrix"];
	$qtySummary		= $g["qtySummary"];
	$challanSummary		= $g["challanSummary"];

	if ($subSupplierChk) {
		$subSupplierName = $dailycatchsummaryObj->getSubSupplier($fromDate, $tillDate, $selectSupplier, $selectADate, $dateSelectFrom);
	}

	$dailyCatchSummaryRecords =  $dailycatchsummaryObj->filterDailyCatchSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);

	//$dateWiseRecords = $dailycatchsummaryObj->fetchDateWiseRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate);

	#process-Count-Summary
	$processCountSummaryRecords = $dailycatchsummaryObj->filterProcessCountSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);

	#For Selecting Fish - Process - Summary
	$processSummaryRecords	= $dailycatchsummaryObj->filterFishProcessSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);

	#For Selecting Fish - Catch - Summary
	$fishWiseCatchSummaryRecords = $dailycatchsummaryObj->filterFishWiseCatchSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany);

	#Wt Challan Wise Summary
	$wtChallanWiseSummary 	= $dailycatchsummaryObj->filterWtChallanRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany);

	#supplier Declared Wt Records(Suplier Memo)
	$declaredWtRecords  = $dailycatchsummaryObj->getSupplierDeclaredWtRecords($selectUnit,$landingCenterId,$selectSupplier,$fromDate,$tillDate,$fishId,$processId, $selectADate, $dateSelectFrom, $billingCompany);

	#For declared wt sheet
	//-----------------
	# For Listing
	$supplierWiseDeclaredRecords = $dailycatchsummaryObj->getSupplierWiseDeclaredRecords($selectUnit,$landingCenterId,$selectSupplier,$fromDate,$tillDate,$fishId,$processId, $selectADate, $dateSelectFrom, $billingCompany);

	$processCountWiseDeclaredRecords = $dailycatchsummaryObj->getProcessCountWiseDeclaredRecords($selectUnit,$landingCenterId,$selectSupplier,$fromDate,$tillDate,$fishId,$processId, $selectADate, $dateSelectFrom, $billingCompany);
	//----------------

	if ($RMMatrix) {
		#RM Summary Matrix
		$RMChallanWiseRecords 	= $dailycatchsummaryObj->groupWtChallanRecords($fromDate, $tillDate, $selectUnit, $landingCenterId, $selectSupplier, $fishId, $processId, $selectADate, $billingCompany);

		$rmSummaryMatrixRecords = $dailycatchsummaryObj->filterRMSummaryMatrixRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);
	}

	#For getting the local qty Report
	if ($localQtyReportChk) {
		$dailyCatchReportResultSetObj = $dailycatchsummaryObj->filterDailyCatchEntryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany);
		$dailyCatchReportRecords = $dailyCatchReportResultSetObj->getNumRows();
	}

	# Rate Summary Matrix
	if ($RMRateMatrix) {
		# Getting RM Supplied Suppliers
		$rmSupplierRecords = $dailycatchsummaryObj->fetchRMSupplierRecords($fromDate, $tillDate, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom, $selectSupplier, $fishId, $processId, $billingCompany);
		# Getting Process Code, Grade Count summary
		$rmSummaryMatrixRecords = $dailycatchsummaryObj->filterRMSummaryMatrixRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);
	}

	# Qty Summary View
	if ($qtySummary) {
		$wtChallanQtySummaryRecs = $dailycatchsummaryObj->fetchWtChallanQtyRecs($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany);
	}

	# Challan Wise Summary
	if ($challanSummary) {
		$challanQtyWiseSummaryRecs = $dailycatchsummaryObj->fetchChallanWiseRecs($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany);
	}


	
	?>
<html>
<head>
<title>DAILY CATCH SUMMARY</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
	function printDoc()
	{
		window.print();	
		return false;
	}

	function displayBtn()
	{
		document.getElementById("printButton").style.display="block";			
	}

	function printThisPage(printbtn)
	{	
		document.getElementById("printButton").style.display="none";	
		if (!printDoc()) {
			setTimeout("displayBtn()",3600); //3000			
		}	
		/*if (!printDoc()) {
			document.getElementById("printButton").style.display="block";			
		}*/		
	}
</script>
<style type="text/css">
@page
{
	/*size: landscape;*/
	margin: 2cm;
}
</style>
<style type="text/css" media="print">
@page
{
	/*size: landscape;*/
	margin: 2cm;
}
</style>
</head>
<body>
<table width="90%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this,'<?=$wNumber?>','<?=$billingCompanyId?>', '<?=$confirm?>');" style="display:block"></td>
</tr>
</table>
<? if($supplierMemo=="" && $declWtSummary==""){ ?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-head" ><font size="4">
	<?=$billcompanyN;//=$companyArr["Name"];?></font> </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item"><font size="3">WEIGHMENT CHALLAN
      REPORT </font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="left" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="left">
	<table width="600" align="center" cellpadding="0" cellspacing="0">
	<tr><td colspan="3">
	<fieldset>
	<table width="500" align="center" cellpadding="0" cellspacing="0">
    <tr>
          <td valign="top">
		  <table cellpadding="0" cellspacing="0">
              <tr>
                <td class="fieldName" nowrap>Material Received at:</td>
                <td class="listing-item">
				<?
				if($selectUnit!=0)	echo $plantName;
				else echo "&nbsp;All" ;
				?></td>
              </tr>
            </table></td>
          <td valign="top">
		  <table width="200" cellpadding="0" cellspacing="0">
              <tr>
                <td class="fieldName" style="padding-left:5px; padding-right:5px;">From:</td>
                <td class="listing-item" nowrap>
                  <?=$dateFrom?>                </td>
                <td class="fieldName" nowrap>&nbsp; Till: </td>
                <td class="listing-item" nowrap>&nbsp;&nbsp;
                  <?=$dateTill?>                </td>
              </tr>
            </table></td>
          </tr>
        <tr>
          <td>
		  <table cellpadding="0" cellspacing="0">
              <tr>
                <td class="fieldName" nowrap>Landing Center:</td>
                <td class="listing-item" nowrap="nowrap">
				<? if($landingCenterId!=0){
				   echo $landingCenterName;
				} else {?> &nbsp;All <? }?></td>
              </tr>
            </table></td>
          <td><table cellpadding="0" cellspacing="0">
              <tr>
                <td class="fieldName" style="padding-left:5px; padding-right:5px;">Supplier:</td>
                <td class="listing-item" nowrap><? if($selectSupplier!=0){
									echo $supplierName;
									} else {?>All<? }?></td>
              </tr>
            </table></td>
          </tr>
      </table></fieldset></td></tr></table></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="left" height="5"></td>
  </tr>
</table>
  <!-- Header Table Ends Here-->
   <? if($details!="" || $proCount!="" || $proSummary!="" || $wChallan!="" || $fishCatchSummary!="" || $wtChallanSummary!="" || $RMMatrix!="" || $localQtyReportChk!="" || $advSearch!="" || $RMRateMatrix!="" || $qtySummary!="" || $challanSummary!="") {?>
   		<?
			if( sizeof($dailyCatchSummaryRecords) && $details!=""){
				$i	=	0;
		?>
  <table cellpadding="0" cellspacing="0">
  <tr bgcolor=white>
    <td colspan="17" align="left">
	<table cellpadding="0" cellspacing="0">
        <tr bgcolor=white>
          <td colspan="17" align="center" height="5"></td>
        </tr>
        <tr bgcolor=white>
          <td colspan="17" align="center" class="fieldName" style="line-height:15px;"><font size="2">Detailed Catch Report</font></td>
        </tr>
        <tr bgcolor=white>
          <td colspan="17" align="center" height="5"></td>
        </tr>
      <tr bgcolor=white>
    <td colspan="17" align="center">
	<table class="print">
                                  <tr bgcolor="#f2f2f2" align="center">
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Date</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Wt Challan No </th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Fish</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Process Code </th>
				<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Decl. Ct</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Decl. Wt</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Grade</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Count</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">NetWt</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Adjust Wt</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Local</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Wstge</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Soft</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Peeling(%)</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Remarks</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Quantity </th>
									<? if($rateNAmount) {?>
				<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Rate</th>
				<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Amount</th>
				<? }?>
                                  </tr>
                                  <?
								  #Finding Total page
								$dailyCatchSummaryRecordSize = sizeof($dailyCatchSummaryRecords);
								$totalPage = ceil($dailyCatchSummaryRecordSize/$numRows);
								$i = 0;
								$j = 0;

								  $gradeCode	=	"";
								  $totalWt	=	"";
								  $grandTotalNetWt	=	"";
								  $grandtotalAdjustWt	=	"";
								  $totalLocalQty	=	"";
								  $totalWastageQty	=	"";
								  $totalSoftQty		=	"";
								  $netselrate="";
									$netselamt="";
								foreach($dailyCatchSummaryRecords as $cer){
									$i++;
									$catchEntryId	=	$cer[0];
									$challanDate = $cer[3];
									$enteredDate = "";
									if($prevRecDate!=$challanDate){
										$array			=	explode("-",$challanDate);
										$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
									}
									$weigmentChellanNo	=	$cer[6];
									$wChellanNo	=	$cer[47];
									$weigmentChellanNo = "";
									if($prevRecWChallanNo!=$wChellanNo){
										$weigmentChellanNo	=	$cer[47];
									}
									$supplier	=	$cer[8];
									$supplierRec	=	$supplierMasterObj->find($supplier);
									$supplierName	=	$supplierRec[2];

									$fishId		=	$cer[11];
									$fishRec	=	$fishmasterObj->find($fishId);
									$fishName	=	$fishRec[1];
									$count		=	$cer[13];
									$local		=	$cer[16];
									$totalLocalQty	+=	$local;
									$wastage		=	$cer[17];
									$totalWastageQty += 	$wastage;

									$soft		=	$cer[18];
									$totalSoftQty	+=	$soft;

									$declCount	=	$cer[30];

									$processCodeId	=	$cer[12];
									$processCodeRec		=	$processcodeObj->find($processCodeId);
									$processCode	=	$processCodeRec[2];

									$adjustWt		=	$cer[20];
									$gradeCountAdj	=	$cer[44];
									$totalAdjustment =	$adjustWt + $gradeCountAdj;

									$grandtotalAdjustWt += $totalAdjustment;

									$reason			=	$cer[19];
									$peeling		=	$cer[22];
									$remarks		=	$cer[23];
									$netWt			=	$cer[26];
									$grandTotalNetWt	+=	$netWt;

									$declWt			=	$cer[29];

									$dailyRateRec	=	$supplieraccountObj->findDailyRate($fishId);
									$declRate		=	$dailyRateRec[7];

									$paidStatus		=	$cer[35];
									$selectWeight	=	$cer[32];
									$selectRate		=	$cer[33];
									$actualRate		=	$cer[34];

									$payableWt		=	$cer[28];
									$totalWt		+=	$payableWt;

									$payableRate	=	$dailyRateRec[6];
									$receivedBy		=	$cer[46];
									$selrate=$cer[48];
									$selamt=$cer[49];
									$netselrate+=$selrate;
									$netselamt+=$selamt;

									$gradeCode	=	"";
									if($count=="" || $receivedBy=='B' || $receivedBy =='G'){
										$gradeRec			=	$grademasterObj->find($cer[37]);
										$gradeCode			=	stripSlash($gradeRec[1]);
									}

									$centerRec			=	$landingcenterObj->find($cer[7]);
									$landingCenterName	=	stripSlash($centerRec[1]);

									if($payableWt!=0) {

								?>
                                  <tr bgcolor="#FFFFFF">
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$enteredDate?></td>
                                  	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$weigmentChellanNo;?></td>
                                    <td class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$fishName?></td>
                                    <td class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$processCode?></td>
				    <td class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$declCount?></td>
                                    <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$declWt?></td>
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$gradeCode?></td>
                                    <td class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$count?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$netWt?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$totalAdjustment?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$local?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$wastage?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$soft?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$peeling?></td>
                                    <td class="listing-item" align="left" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$remarks?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$payableWt?></td>
									<? if($rateNAmount) {?>
									<td class="listing-item" align="left" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$selrate?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$selamt?></td>
									<?php }?>



                                  </tr>
								   <?
	  							if($i%$numRows==0 && $dailyCatchSummaryRecordSize!=$numRows)
									{
										$j++;
			  					?>
		    </table></td></tr>
			<tr><td><table width="98%" cellpadding="2">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;font-size:10px"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;font-size:10px">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td></tr>
	<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
	  </table></td></tr></table>
		<!-- Setting Page Break start Here-->
	<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->	
								  <table>
								  <tr>
								    <td colspan="17" class="listing-head" align="center"><font size="3"><?=$companyArr["Name"];?></font></td>
								    </tr>
								  <tr>
								    <td colspan="17" height="5"></td>
								    </tr>
								  <tr>
								  <td colspan="17">
									<table cellpadding="0" cellspacing="0">
									<tr>
									  <td class="fieldName" style="line-height:15px;" align="center"><font size="2">Detailed Catch Report</font>- Cont.</td>
									  </tr>
									<tr><td height="5"></td></tr>
									 <tr>
									 <td colspan="17">
									 <table class="print">
                                  <tr bgcolor="#f2f2f2" align="center">
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Date</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Wt Challan No </th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Fish</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Process Code </th>
									  <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Decl. Ct</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Decl. Wt</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Grade</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Count</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">NetWt</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Adjust Wt</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Local</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Wstge</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Soft</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Peeling(%)</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Remarks</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Quantity </th>
									<? if($rateNAmount) {?>
				<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Rate</th>
				<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Amount</th>
				<? }?>
                                  </tr>

                                  <?
						}
					  } //Effective wt 0 end here
					  $prevRecDate=$challanDate;
					  $prevRecWChallanNo=$wChellanNo;
					  }
								  ?>
								  <tr bgcolor="#FFFFFF">
                                    <td colspan="8" align="right" nowrap class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">TOTAL:</td>
                                    <td nowrap class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong><? echo number_format($grandTotalNetWt,2);?></strong></td>
                                    <td nowrap class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong><? echo number_format($grandtotalAdjustWt,2);?></strong></td>
                                    <td nowrap class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong><? echo number_format($totalLocalQty,2);?></strong></td>
                                    <td nowrap class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong><? echo number_format($totalWastageQty,2);?></strong></td>
                                    <td nowrap class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong><? echo number_format($totalSoftQty,2);?></strong></td>
                                    <td nowrap class="listing-item" align="right">&nbsp;</td>
                                    <td nowrap class="listing-item" align="right">&nbsp;</td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong>                   <? echo number_format($totalWt,2);?></strong></td>
									<? if($rateNAmount) {?>
									  <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong>                  <? //echo number_format($netselrate,2);?></strong></td>
									    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong>                   <? echo number_format($netselamt,2);?></strong></td>
										<?php }?>
                                  </tr>
		    </table>		  </td>
	    </tr>


  <tr bgcolor=white>
    <td colspan="17" align="center">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr bgcolor="#FFFFFF">
          <td align="center" height="5"></td>
        </tr>
		 <tr><td><table width="98%" align="center" cellpadding="2">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td></tr>	
	<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
    </table>

	</td></tr></table>
	</td></tr></table>
	 <? }?>
	<?
	  if( sizeof($processCountSummaryRecords) && $proCount!=""){
	?>
		<table align="center">
        <tr bgcolor=white>
          <td align="left">
		  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">

              <tr bgcolor="white">
                <td colspan="5" align="center" class="fieldName" style="line-height:15px;" nowrap><font size="2">Summary of Processes-Count Wise Catch Received</font></td>
              </tr>
              <tr bgcolor="white">
                <td colspan="5" align="left" height="5"></td>
              </tr>
              <tr bgcolor="white">
                <td colspan="5" align="center">
				<table cellpadding="2" cellspacing="0" class="print">
				  <tr bgcolor="#f2f2f2" align="center">
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Fish</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Grade</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Count</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Quantity</th>
				<? if($rateNAmount) {?>
				<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="180">Rate</th>
				<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="200">Amount</th>
				<? }?>
              </tr>
              <?

				$totalWt = "";
				$gradeCode	=	"";

				#Finding Total page
				$processCountSummaryRecordSize = sizeof($processCountSummaryRecords);
				$totalPage = ceil($processCountSummaryRecordSize/$numRows);
				$i = 0;
				$j = 0;
				$netPamount=0;
					$prate=0;
					$pamount=0;
					$netPrate=0;

				foreach($processCountSummaryRecords	 as $cer){
					$i++;
					$catchEntryId	=	$cer[0];

					$fishId			=	$cer[1];
					$fishRec		=	$fishmasterObj->find($fishId);
					$fishName		=	$fishRec[1];

					$processCodeId	=	$cer[2];
					$processCodeRec	=	$processcodeObj->find($processCodeId);
					$processCode	=	$processCodeRec[2];

					$count			=	$cer[3];

					$declCount		=	$cer[4];

					$declWt			=	$cer[5];


					$effectiveWt	=	$cer[9];
					$totalWt	+=	$effectiveWt;
					$pamount=$cer[15];
						$prate=$cer[16];
						$netPrate+=$prate;
						$netPamount+=$pamount;

					$receivedBy	=	$cer[8];
					$gradeCode	=	"";
					if($count=="" || $receivedBy=='B' || $receivedBy=='G'){
						$gradeRec			=	$grademasterObj->find($cer[7]);
						$gradeCode			=	stripSlash($gradeRec[1]);
					}

					if($effectiveWt!=0) {
				?>
              <tr bgcolor="#FFFFFF">
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$fishName?></td>
                <td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$processCode?></td>
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$gradeCode?></td>
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$count?></td>
                <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$effectiveWt?></td>
				  <? if($rateNAmount) {?>
				<td class="listing-item">&nbsp;<?=$prate;?></td>
				  <td class="listing-item">&nbsp;<?=$pamount;?></td>
				  <? }?>
              </tr>
			   <?

			if($i%$numRows==0 && $processCountSummaryRecordSize!=$numRows)
			{
				$j++;
			  ?>
			  </table></td></tr>
			  <tr>
			  <td><table width="98%" cellpadding="3">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td></tr>
			  <tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
          </table>
</td></tr></table>
	  <!-- Setting Page Break start Here-->
	<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table align="center">
	  <tr>
	    <td class="listing-head" align="center"><font size="3"><?=$companyArr["Name"];?></font></td>
	    </tr>
	  <tr>
	    <td height="5"></td>
	    </tr>
	  <tr><td>
			  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
			  <tr bgcolor="white">
                <td colspan="5" align="center" class="fieldName" style="line-height:15px;" nowrap><font size="2">Summary of Processes-Count Wise Catch Received</font> - Cont.</td>
              </tr>
              <tr bgcolor="white">
                <td colspan="5" align="left" height="5"></td>
              </tr>
			  <tr>
			  <td>
			  <table cellpadding="2" cellspacing="0" class="print">
			  <tr bgcolor="#f2f2f2" align="center">
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Fish</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Grade</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Count</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Quantity</th>
				<? if($rateNAmount) {?>
				<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="180">Rate</th>
				<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="200">Amount</th>
				<? }?>
              </tr>
              <?
		}
		   } // Effective Wt 0 Checking Loop end here
			  }

			  ?>
              <tr bgcolor="#FFFFFF">
                <td class="listing-item" nowrap>&nbsp;</td>
                <td class="listing-item">&nbsp;</td>
                <td class="listing-item" nowrap>&nbsp;</td>
                <td nowrap class="listing-head" align="center">TOTAL</td>
                <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalWt,2);?></strong></td>
				  <? if($rateNAmount) {?>
			 <td>&nbsp;<strong><? //echo number_format($netPrate,2);?></strong></td>
				  <td>&nbsp;<strong><?echo number_format($netPamount,2);?></strong></td>
				  <? }?>
              </tr></table></td>
              </tr>
			  <tr><td><table width="98%" align="center" cellpadding="3">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td></tr>
<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
            </table></td>
        </tr>
</table>
			<?
				  }
			 ?>
			 <?
				  if( sizeof($processSummaryRecords) && $proSummary!=""){
			  ?>
		<table align="center">
        <tr bgcolor=white>
          <td>
		  <table width="60%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr bgcolor="white">
                <td colspan="3" align="left" nowrap="nowrap" height="5"></td>
              </tr>
              <tr bgcolor="white">
                <td colspan="3" align="center" class="fieldName" style="line-height:15px;" nowrap="nowrap"><font size="2">Summary of Processes wise Catch Received</font></td>
              </tr>
              <tr bgcolor="white">
                <td colspan="3" align="left" height="10"></td>
              </tr>
              <tr bgcolor="white">
                <td colspan="3" align="left">
				<table align="center" cellpadding="0" cellspacing="0" class="print">
                  <tr bgcolor="#f2f2f2" align="center">
                <th class="listing-head" style="padding-left:10px; padding-right:10px;">Fish</th>
                <th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="nowrap">Process Code</th>
                <th class="listing-head" style="padding-left:10px; padding-right:10px;">Effective Wt</th>
				<? if($rateNAmount) {?>
				 <th class="listing-head" style="padding-left:10px; padding-right:10px;">Rate</th>
				  <th class="listing-head" style="padding-left:10px; padding-right:10px;">Amount</th>
				<?php }?>



              </tr>
              <?
			 #Finding Total page
			$processSummaryRecordSize = sizeof($processSummaryRecords);
			$totalPage = ceil($processSummaryRecordSize/$proSummaryNumRows);
			$i = 0;
			$j = 0;
			$totalWt ="";
			$pramount=0;
			$netPramount=0;
			foreach($processSummaryRecords as $psr){
				$i++;

				$fishRec		=	$fishmasterObj->find($psr[1]);
				$fishName		=	$fishRec[1];

				$processCodeId	=	$psr[2];
				$processCodeRec	=	$processcodeObj->find($processCodeId); //Find Process Code
				$processCode	=	$processCodeRec[2];				
				$totalQty	=	$psr[4];
				$totalWt	+=	$totalQty;
				$pramount=$psr[11];
				$netPramount+=$pramount;
				$ratePSF=$pramount/$totalQty;
				$netratePSF+=$ratePSF;
				if($totalQty!=0) {
			?>
              <tr bgcolor="#FFFFFF">
                <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"> <?=$fishName?></td>
                <td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap="nowrap"><?=$processCode?></td>
                <td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"> <?=$totalQty?></td>
				<? if($rateNAmount) {?>
				<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;">&nbsp;<?=number_format($ratePSF,2,'.','');?></td>
				  <td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;">&nbsp;<?=$pramount?></td>
				<?php }?>
              </tr>
			  <?

				if($i%$proSummaryNumRows==0 && $processSummaryRecordSize!=$proSummaryNumRows)
					{
						$j++;
			  ?>
			  </table>			  </td></tr>
              <tr bgcolor="white">
                <td colspan="3"><table width="98%" cellpadding="3">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
              </tr>
		<tr bgcolor="#FFFFFF">
		<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
        <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <!--<tr>
	    <td colspan="6" valign="bottom" nowrap="nowrap" class="listing-item" style="line-height:8px;" align="right">(Page <?=$j?> of <?=$totalPage?>)</td>
	    </tr>-->
		<tr><TD colspan="6" height="5"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    	</table></td></tr>
          </table>
		  </td></tr></table>
		<!-- Setting Page Break start Here-->
	<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
			  <table align="center">
			   <tr><td class="listing-head" align="center"><font size="3"><?=$companyArr["Name"];?></font></td></tr>
			  	<tr>
					<td>
						<table width="60%" border="0" align="center" cellpadding="0">
						<tr bgcolor=white>
    <td colspan="17" align="left">
	<table width="600" align="center" cellpadding="0" cellspacing="0">
	<tr><td colspan="3">
	<fieldset>
	<table width="500" align="center" cellpadding="0" cellspacing="0">
	<tr bgcolor="white">
                <td colspan="3" align="center"  style="line-height:15px;" nowrap="nowrap">
			<table>
				<TR>
		<TD class="fieldName" nowrap="true"><font size="2">Summary of Processes wise Catch Received</font>- Cont.</TD>
		<TD>
			<table width="200" cellpadding="0" cellspacing="0">
              <tr>
                <td class="fieldName" style="padding-left:5px; padding-right:5px;">From:</td>
                <td class="listing-item" nowrap><?=$dateFrom?></td>
                <td class="fieldName" nowrap>&nbsp; Till:</td>
                <td class="listing-item" nowrap><?=$dateTill?></td>
              </tr>
            </table></TD>
		</TR>
			</table>
			
		</td>
              </tr>        
      </table></fieldset></td></tr></table></td>
  </tr>
						<tr bgcolor="white">
                <td colspan="3" align="left" nowrap="nowrap" height="5"></td>
              </tr>
              <!--<tr bgcolor="white">
                <td colspan="3" align="center"  style="line-height:15px;" nowrap="nowrap">
			<table>
				<TR>
					<TD>
			<table width="200" cellpadding="0" cellspacing="0">
              <tr>
                <td class="fieldName" style="padding-left:5px; padding-right:5px;">From:</td>
                <td class="listing-item" nowrap>
                  <?=$dateFrom?>                </td>
                <td class="fieldName" nowrap>&nbsp; Till: </td>
                <td class="listing-item" nowrap>&nbsp;&nbsp;
                  <?=$dateTill?>                </td>
              </tr>
            </table></TD>
					<TD class="fieldName"><font size="2">Summary of Processes wise Catch Received</font>- Cont.</TD>
				</TR>
			</table>
			
		</td>
              </tr>-->
              <tr bgcolor="white">
                <td colspan="3" align="left" height="10"></td>
              </tr>
						  <tr>
						   <td colspan="3">
						    <table align="center" cellpadding="0" cellspacing="0" class="print">
                  			<tr bgcolor="#f2f2f2" align="center">
							<th class="listing-head" style="padding-left:10px; padding-right:10px;">Fish</th>
							<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="nowrap">Process Code</th>
							<th class="listing-head" style="padding-left:10px; padding-right:10px;">Quantity</th>
							<th class="listing-head" style="padding-left:10px; padding-right:10px;">Amount</th>
              				</tr>

              <?
			  		}
				} //Effective Wt 0 checking end here
			  }
			  ?>
              <tr bgcolor="#FFFFFF">
                <td class="listing-item" nowrap>&nbsp;</td>
                <td nowrap class="listing-head" align="center">TOTAL</td>
              <td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"> <strong><? echo number_format($totalWt,2);?></strong></td>
			  <? if($rateNAmount) {?>
				<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;">&nbsp;<strong><?//=number_format($netratePSF,2,'.','');?></strong></td>
				  <td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"> <strong><? echo number_format($netPramount,2);?></strong></td>
				  <?php }?>
              </tr></table></td>
			  <td>&nbsp;</td>
			
              </tr>
						  <tr>
						    <td colspan="3"><table width="98%" align="center" cellpadding="3">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td></tr>	
	<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td></tr>	
         </table>
	</td>
        </tr>
</table>
		<?
			  }
		?>
		 <?
			  if( sizeof($fishWiseCatchSummaryRecords) && $fishCatchSummary!=""){
		 ?>
		<table align="center">
        <tr bgcolor="white">
          <td>
	<table width="50%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr bgcolor="white">
                <td colspan="2" align="left" height="5"></td>
              </tr>
              <tr bgcolor="white">
                <td colspan="2" align="center" class="fieldName" nowrap="nowrap"><font size="2">Summary of Catch received</font></td>
              </tr>
              <tr bgcolor="white">
                <td colspan="2" align="left" height="10"></td>
              </tr>
              <tr bgcolor="white">
                <td colspan="2">
			<table width="250" align="center" cellpadding="0" cellspacing="0" class="print">
                  <tr bgcolor="#f2f2f2" align="center">
                	<th width="25%" class="listing-head" style="padding-left:10px; padding-right:10px;">Fish</th>
                	<th width="25%" class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Quantity</th>
					<? if($rateNAmount) {?>
				<!--<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="180">Rate</th>-->
				<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="200">Amount</th>
				<? }?>
              	</tr>
              <?

				#Finding Total page
				$fishWiseCatchSummaryRecordSize = sizeof($fishWiseCatchSummaryRecords);
				$totalPage = ceil($fishWiseCatchSummaryRecordSize/$numRows);
				$i = 0;
				$j = 0;
				$totalWt ="";
				$nettotalAmtfs	=0;
				foreach($fishWiseCatchSummaryRecords as $fcr){
					$i++;

					$fishRec		=	$fishmasterObj->find($fcr[1]);
					$fishName		=	$fishRec[1];

					$processCodeId	=	$fcr[2];
					$processCodeRec		=	$processcodeObj->find($processCodeId);
					$processCode	=	$processCodeRec[2];

					$totalQty		=	$fcr[4];

					$totalWt	+=	$totalQty;
					$ratefc=$fcr[5];
		$totalAmt		=	$fcr[6];
		$nettotalAmtfs	+=	$totalAmt;
		$netratefc+=$ratefc;
		//$nettotalAmtfs	+=	$totalAmt;
			?>
              <tr bgcolor="#FFFFFF">
                <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$fishName?></td>
                <td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"> <?=$totalQty?></td>
				<? if($rateNAmount) {?>
				<!--<th class="listing-item" align="right" style="padding-left:10px; padding-right:10px;">&nbsp;</th>-->
				<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=$totalAmt;?></td>
				<? }?>
              </tr>
			  <?

				if($i%$numRows==0 && $fishWiseCatchSummaryRecordSize!=$numRows)
					{
						$j++;
			  ?>
			  </table>			  </td></tr>
              <tr bgcolor="white">
                <td colspan="2"><table width="98%" cellpadding="3">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
              </tr>
	<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
		  </table>
		  </td></tr></table>
	<!-- Setting Page Break start Here-->
	<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
			  <table align="center">
			   <tr><td class="listing-head" align="center"><font size="3"><?=$companyArr["Name"];?></font></td></tr>
			   <tr>
			   	<td>
				 <table width="50%" border="0" align="center" cellpadding="0" cellspacing="0">
				  <tr bgcolor="white">
                <td colspan="2" align="left" height="5"></td>
              </tr>
              <tr bgcolor="white">
                <td colspan="2" align="center" class="fieldName" nowrap="nowrap"><font size="2">Summary of Catch received</font>- Cont.</td>
              </tr>
              <tr bgcolor="white">
                <td colspan="2" align="left" height="10"></td>
              </tr>
				  <tr>
				   <td>
				   	<table width="250" align="center" cellpadding="0" cellspacing="0" class="print">
                  	 <tr bgcolor="#f2f2f2" align="center">
                	  <th width="25%" class="listing-head" style="padding-left:10px; padding-right:10px;">Fish</th>
                	  <th width="25%" class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Quantity</th>
					  <? if($rateNAmount) {?>
				<!--<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="180">Rate</th>-->
				<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="200">Amount</th>
				<? }?>
              	    </tr>
              <?
			  		}
			   }
			   ?>
              <tr bgcolor="#FFFFFF">
                <td nowrap class="listing-head" align="center">TOTAL</td>
                <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><strong><? echo number_format($totalWt,2);?></strong></td>
				<? if($rateNAmount) {?>
				 <!--<td class="listing-item" align="right" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><strong><? //echo number_format($totalWt,2);?></strong></td>-->
				  <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><strong> <?=number_format($nettotalAmtfs,2);?></strong></td>
				  <?php }?>
              </tr></table></td>
              </tr>
				  <tr>
				    <td><table width="98%" align="center" cellpadding="3">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
	      </tr>
	<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
            </table></td>
        </tr>
</table>
	<?
	  }
	?>
		 <?
		  if( sizeof($wtChallanWiseSummary) && $wtChallanSummary!=""){
		?>
		<table align="center">
		<tr><td>
		<table border="0" cellspacing="0" cellpadding="0">

              <tr bgcolor="white">
                <td colspan="2" align="center" class="fieldName" style="line-height:15px;"><font size="2">Summary of Wt Challan Wise Catch Received</font></td>
              </tr>
              <tr bgcolor="white">
                <td colspan="2" align="left" height="5"></td>
              </tr>
              <tr bgcolor="white">
                <td colspan="2" align="left">
				<table class="print">

                                  <tr bgcolor="#f2f2f2" align="center">
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Date</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Wt Challan No </th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Supplier</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Fish</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Process Code </th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Count</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Grade</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Quantity </th>
									<? if($rateNAmount) {?>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px" width="180">Rate</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px" width="180">Amount</th>
									<? }?>
                                  </tr>
                         <?php

						$totalWt	=	"";
						$totalPage  = "";
						#Finding Total page
						$wtChallanWiseSummaryRecordSize = sizeof($wtChallanWiseSummary);
						$totalPage = ceil($wtChallanWiseSummaryRecordSize/$wtChllanSumryNumRows);
						$i = 0;
						$j = 0;
						$totalCrate="";
						$totalCamount="";
						foreach($wtChallanWiseSummary as $wcs){
						 $i++;
						$entryDate		=	$wcs[1];


						$wtChallanNo	=	$wcs[17];
						$weighmentChallanNo = "";
						if($wtChallanNo!=$prevWtChallanNo)
						{
							$weighmentChallanNo	=	$wcs[17];
						}


						$enteredDate = "";
						if($entryDate!=$prevEntryDate || $wtChallanNo!=$prevWtChallanNo){
							$array			=	explode("-",$wcs[1]);
							$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
						}

						$supplierId			=	$wcs[2];
						$supplierName = "";
						if($supplierId!=$prevSupplierId || $wtChallanNo!=$prevWtChallanNo)
						{
							$supplierName		=	$wcs[9];
						}
						$fishName			=	$wcs[10];
						$processCode		=	$wcs[11];

						$countValues		=	$wcs[6];
						$grade	= "";
						if($countValues=="")
						{
							$grade				=	$wcs[12];
						}
						$effectiveWt	=	$wcs[8];
						$totalWt	+=	$effectiveWt;
						$crate=$wcs[18];
						$camount=$wcs[19];
						$totalCrate+=$crate;
						$totalCamount+=$camount;
						if($effectiveWt!=0) {

			?>
                                  <tr bgcolor="#FFFFFF">
                                    <td class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$enteredDate?></td>
                                    <td class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$weighmentChallanNo?></td>
                                    <td class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$supplierName?></td>
                                    <td class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$fishName?></td>
                                    <td class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$processCode?></td>
                                    <td class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$countValues?></td>
                                    <td class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$grade?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><?=$effectiveWt?></td>
									<? if($rateNAmount) {?>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$crate;?>&nbsp;</td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$camount;?>&nbsp;</td>
				<? }?>
                                  </tr>
				  <?
	  				if($i%$wtChllanSumryNumRows==0 && $wtChallanWiseSummaryRecordSize!=$wtChllanSumryNumRows)
					{
						$j++;
				  ?>
  				  </table></td></tr>
				  <tr>
				<td>
				<table width="98%" align="center" cellpadding="2">
		<tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; padding-left:5px; padding-right:5px; font-size:10px"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; padding-left:5px; padding-right:5px; font-size:10px">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table>
					</td>
				  </tr>
		<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
		  </table></td></tr></table>
					  <!-- Setting Page Break start Here-->
					<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->	
								  <table align="center">
								  <tr>
								    <td align="center" class="listing-head"><font size="3"><?=$companyArr["Name"];?></font></td>
								    </tr>
								  <tr>
								    <td height="5"></td>
								    </tr>
								  <tr>
								  <td>
								  <table border="0" cellspacing="0" cellpadding="0">
								  <tr>
								    <td colspan="2" align="center" class="fieldName" style="line-height:15px;"><font size="2">Summary of Wt Challan Wise Catch Received</font>- Cont.</td>
								    </tr>
								  <tr><td colspan="2" align="center" height="5"></td></tr>
								  <tr>
								  	<td colspan="2" align="left">
									<table class="print">
                                  <tr bgcolor="#f2f2f2" align="center">
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Date</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Wt Challan No </th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Supplier</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Fish</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Process Code </th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Count</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Grade</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">Quantity </th>
									<? if($rateNAmount) {?>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px" width="180">Rate</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px" width="180">Amount</th>
									<? }?>
                                  </tr>
                                  <?
								   }
						} // effective Wt 0 Checking End Here
						  $prevWtChallanNo 	= 	$wtChallanNo;
						  $prevSupplierId	=	$supplierId;
						  $prevEntryDate	=	$entryDate;
					 }
				  ?>
								   <tr bgcolor="#FFFFFF">
                                    <td colspan="7" align="right" nowrap class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px">TOTAL :&nbsp;&nbsp;</td>
                                    <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px"><strong>
                  <? echo number_format($totalWt,2);?>
                  </strong></td>
				  <? if($rateNAmount) {?>
                                    <td class="listing-item" align="right" nowrap="nowrap"><strong><? //echo number_format($totalCrate,2);?></strong></td>
                                    <td class="listing-item" align="right" nowrap="nowrap"><strong><? echo number_format($totalCamount,2);?></strong>&nbsp;</td>
									<? }?>
							      </tr>
                  </table></td>
              </tr>
            <tr>
			 <td>
			 	<table width="98%" align="center" cellpadding="2">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; padding-left:5px; padding-right:5px; font-size:10px"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; padding-left:5px; padding-right:5px; font-size:10px">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table>			 </td>
			</tr>
	<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
            </table></td></tr>
			</table>

			<?
				  }
			 ?>
			<table cellpadding="0" cellspacing="0">
			<tr><td>&nbsp;</td></tr>
        <? } else {?>
        <tr bgcolor="white">
          <td  class="err1" height="5" align="center">
            <?=$msgNoRecords;?>          </td>
        </tr>
        <? } ?>
      </table></td>
  </tr>
</table></td></tr>
</table>
	<?
	}
	if($supplierMemo!="")
	{
	?>
<!-- Supplier Memo Printing -->
<table width="90%" align="center" cellpadding="0" cellspacing="0" class="boarder">
  <tr><td>
<table width="100%" align="center" bgcolor="#FFFFFF">
	<tr>
	  <td>
	  <table width="100%">
	    <tr><td>
	  <table width="100%" border="0" align="center">
        <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-head" ><font size="3"><?=$companyArr["Name"];?></font> </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item"><font size="2">WEIGHMENT CHALLAN
      REPORT </font></td>
  </tr>
      </table>
	  </td></tr>
	    <tr>
	      <td height="10"></td>
        </tr>
	    <tr><td>
	  <table width="450" border="0" align="center" cellpadding="0" cellspacing="0">
	  <?
	  $supplierRec	=	$supplierMasterObj->find($selectSupplier);
	  $supplierName	=	$supplierRec[2];
	  ?>
        <tr>
          <td colspan="2" nowrap="nowrap" class="listing-head"><table width="200" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td class="listing-head" nowrap="nowrap">Supplier Name: </td>
              <td class="listing-item" nowrap><?=$supplierName?></td>
            </tr>
	    <?if ($subSupplierChk) {?>
	<tr><TD height="5"></TD></tr>
	  <tr>
              <td class="listing-head" nowrap>Sub-Supplier Name: </td>
              <td class="listing-item" nowrap><?=$subSupplierName?></td>
            </tr>
	<? }?>

          </table></td>
          </tr>

        <tr>
          <td colspan="2" height="5"></td>
        </tr>
        <tr>
          <td colspan="2"><table width="200" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td class="listing-head" nowrap>Supplied From:</td>
              <td class="listing-item" nowrap="nowrap">&nbsp;<?=$dateFrom?></td>
		<td class="listing-head" nowrap>&nbsp;&nbsp;Till:</td>
              <td class="listing-item" nowrap="nowrap">&nbsp;<?=$dateTill?></td>
            </tr>
          </table></td>
          </tr>
      </table></td></tr>
	  <tr>
	    <td height="10"></td>
	    </tr>
	  </table>	  </td>
	</tr>

	  <?
	   if( sizeof($declaredWtRecords) && $supplierMemo!=""){
	?>
  <tr>
	<td colspan="17">
		<table width="80%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">


                                  <tr bgcolor="#f2f2f2" align="center">
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px;" width="80">Fish</th>
                                    <th class="listing-head"  style="padding-left:5px; padding-right:5px;">Process Code</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px;">Grade/Count</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px;">Decl.Qty</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px;" width="80">Rate</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px;" width="100">Amount</th>
                                  </tr>
                                  <?
							#Finding Total page
							$declaredWtRecordSize = sizeof($declaredWtRecords);
							$totalPage = ceil($declaredWtRecordSize/$numRows);
							$i = 0;
							$j = 0;
							$gradeCode="";
							$totalWt	=	"";
							$prevFishId = 0;
							$prevProcessCodeId = 0;
							foreach($declaredWtRecords as $sdr){
								$i++;

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

								?>
                               <tr bgcolor="#FFFFFF">
                                  <td class="listing-item" style="padding-left:5px; padding-right:5px;" height="30"><?=$fishName?></td>
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$processCode?></td>
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$declCount?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$declWt?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
              					</tr>
								<?

								if($i%$numRows==0 && $declaredWtRecordSize!=$numRows)
									{
										$j++;
								  ?>
								</table>								</td></tr>
  <tr>
    <td colspan="17"><table width="98%" align="center" cellpadding="3">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
  </tr>
</table>
								</td></tr></table>
								<!-- Setting Page Break start Here-->
								<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
								<table width="90%" align="center" cellpadding="0" cellspacing="0" class="boarder">
								 <tr>
								  <td>
								   <table width="100%" align="center" bgcolor="#FFFFFF">
								    <tr>
								      <td colspan="17"><table width="100%">
	    <tr><td>
	  <table width="100%" border="0" align="center">
        <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-head" ><font size="3"><?=$companyArr["Name"];?></font> </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item"><font size="2">WEIGHMENT CHALLAN
      REPORT </font></td>
  </tr>
      </table>
	  </td></tr>
	    <tr>
	      <td height="10"></td>
        </tr>
	    <tr><td>
	  <table width="450" border="0" align="center" cellpadding="0" cellspacing="0">
	  <?
	  $supplierRec	=	$supplierMasterObj->find($selectSupplier);
	  $supplierName	=	$supplierRec[2];
	  ?>
        <tr>
          <td colspan="2" nowrap="nowrap" class="listing-head"><table width="200" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td class="listing-head" nowrap="nowrap">Supplier Name: </td>
          <td class="listing-item" nowrap><?=$supplierName?></td>
            </tr>
	 <?if ($subSupplierChk) {?>
	<tr><TD height="5"></TD></tr>
	  <tr>
              <td class="listing-head" nowrap>Sub-Supplier Name: </td>
              <td class="listing-item" nowrap><?=$subSupplierName?></td>
            </tr>
	<? }?>
          </table></td>
          </tr>

        <tr>
          <td colspan="2" height="5"></td>
        </tr>
        <tr>
          <td colspan="2"><table width="200" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td class="listing-head" nowrap>Supplied From:</td>
              <td class="listing-item" nowrap="nowrap">&nbsp;<?=$dateFrom?></td>
			  <td class="listing-head" nowrap>&nbsp;&nbsp;Till:</td>
              <td class="listing-item" nowrap="nowrap">&nbsp;<?=$dateTill?></td>
            </tr>
          </table></td>
          </tr>
      </table></td></tr>
	  <tr>
	    <td height="10"></td>
	    </tr>
	  </table></td>
							         </tr>
								    <tr>
									 <td colspan="17">
									  <table width="80%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
							  	       <tr bgcolor="#f2f2f2" align="center">
                                        <th class="listing-head" style="padding-left:5px; padding-right:5px;" width="80">Fish</th>
                                        <th class="listing-head"  style="padding-left:5px; padding-right:5px;">Process Code</th>
                                        <th class="listing-head" style="padding-left:5px; padding-right:5px;">Grade/Count</th>
                                        <th class="listing-head" style="padding-left:5px; padding-right:5px;">Decl.Qty</th>
                                        <th class="listing-head" style="padding-left:5px; padding-right:5px;" width="80">Rate</th>
                                        <th class="listing-head" style="padding-left:5px; padding-right:5px;" width="100">Amount</th>
                                      </tr>
                                  <?
								  	}
								  $prevFishId = $fishId;
								  $prevProcessCodeId = $processCodeId;
								  }
								  ?>
								   <tr bgcolor="#FFFFFF">
                                    <td colspan="3" nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;" height="30">TOTAL:</td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalWt,2);?></strong></td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
                                  </tr>
            </table></td></tr>
	    <tr>
		      <td colspan="17">
	<table width="98%" align="center" cellpadding="3">
      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
							         </tr>
								<? } else {?>
								<tr bgcolor="white">
                              <td colspan="17"  class="err1" height="5" align="center">
                                <?=$msgNoRecords;?>                              </td>
                            </tr>
								<? }?>
</table>
</td></tr></table>
<? }

if($declWtSummary!="")
	{
	?>
<!-- Declared Wt summary Printing -->
<table width="90%" align="center" cellpadding="0" cellspacing="0" class="boarder">
  <tr><td>
<table width="100%" align="center" bgcolor="#FFFFFF">
	<tr>
	  <td>
	  <table width="100%">
	    <tr><td>
	  <table width="100%" border="0" align="center">
        <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-head" ><font size="3"><?=$companyArr["Name"];?></font> </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item"><font size="2">WEIGHMENT CHALLAN
      REPORT </font></td>
  </tr>
      </table>
	  </td></tr>
	    <tr>
	      <td height="5"></td>
        </tr>
	    <tr><td>
	  <table width="450" border="0" align="center" cellpadding="0" cellspacing="0">
	  <?
	  $supplierRec	=	$supplierMasterObj->find($selectSupplier);
	  $supplierName	=	$supplierRec[2];
	  ?>
        <tr>
          <td colspan="2" nowrap="nowrap" class="listing-head"><table width="200" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td class="listing-head" nowrap="nowrap">Supplier Name: </td>
          <td class="listing-item" nowrap><?=$supplierName?></td>
            </tr>
	 <?if ($subSupplierChk) {?>
	<tr><TD height="5"></TD></tr>
	  <tr>
              <td class="listing-head" nowrap>Sub-Supplier Name: </td>
              <td class="listing-item" nowrap><?=$subSupplierName?></td>
            </tr>
	<? }?>
          </table></td>
          </tr>

        <tr>
          <td colspan="2" height="5"></td>
        </tr>
        <tr>
          <td colspan="2"><table width="200" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td class="listing-head" nowrap>Supplied From:</td>
              <td class="listing-item" nowrap="nowrap"><?=$dateFrom?></td>
			  <td class="listing-head" nowrap>&nbsp;&nbsp;Till:</td>
              <td class="listing-item" nowrap="nowrap"><?=$dateTill?></td>
            </tr>
          </table></td>
          </tr>
      </table></td></tr>
	  <tr>
	    <td height="5"></td>
	    </tr>
	  </table>	  </td>
	</tr>

	  <?
	   if( sizeof($supplierWiseDeclaredRecords) && $declWtSummary!=""){
	?>
  <tr>
	<td colspan="17">
		<table width="70%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
							  	<? $columnSize = sizeof($processCountWiseDeclaredRecords);?>


                                  <tr bgcolor="#f2f2f2">
                                    <td colspan="2" class="listing-head">&nbsp;</td>
                                    <td class="listing-head" colspan="<?=$columnSize?>" align="center">Process Code &amp; Decl.Count </td>
                                    <td class="listing-head" align="center">&nbsp;</td>
                                  </tr>
                                  <tr bgcolor="#f2f2f2" align="center">
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier RM Challan Date </th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier RM Challan No </th>
									<?

									foreach($processCountWiseDeclaredRecords as $ssr){
										$processCode 	= $ssr[8];
										$declCount 		= $ssr[6];
									?>
                                    <th class="listing-head">
                                      <table width="50" align="center" class="noBoarder">
                                        <tr >
                                          <td class="listing-head" align="center" bgcolor="#999999"><?=$processCode?></td>
                                        </tr>
                                        <tr>
                                          <td class="listing-head" align="center"><?=$declCount?></td>
                                        </tr>
                                      </table></th>
									  <? }?>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px;">Total.Qty</th>
                                  </tr>
                                  <?

								  #Finding Total page
							$supplierWiseDeclaredRecordSize = sizeof($supplierWiseDeclaredRecords);
							$totalPage = ceil($supplierWiseDeclaredRecordSize/$numRows);
							$i = 0;
							$j = 0;
							$gradeCode="";
							$totalWt	=	"";

							foreach($supplierWiseDeclaredRecords as $ssr){
								$i++;
								$catchEntryId	=	$sdr[0];
								$supplierChallanNo = $ssr[3];
								$sChallanDate = $ssr[4];
								$supplierChallanDate = "";
								if($prevRecDate!=$sChallanDate){
									$array			=	explode("-",$sChallanDate);
									$supplierChallanDate	=	$array[2]."/".$array[1]."/".$array[0];
								}

								?>
                               <tr bgcolor="#FFFFFF">
                                  <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" height="30"><?=$supplierChallanDate?></td>
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$supplierChallanNo?></td>
					<?
					$declQty = "";
					$totalDeclQty = 0;

					$qty = 0;
					foreach($processCountWiseDeclaredRecords as $ssr)
					{
						$processCodeId 	= $ssr[2];
						$declCount 		= $ssr[6];
						#finding Declared wt for each cell
						$declQty = $dailycatchsummaryObj->getDeclaredWt($sChallanDate, $supplierChallanNo, $declCount, $processCodeId);

						$totalDeclQty +=$declQty;
					?>
                                    <td class="listing-item" nowrap  align="right" style="padding-left:5px; padding-right:5px;"><? if ($declQty>0) echo number_format($declQty,2,'.',''); else echo $declQty; ?></td>
									<? }?>
									<?
									$grandTotalWt+=$totalDeclQty;
									?>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><? echo number_format($totalDeclQty,2);?></td>
              </tr>
			  				<?

								if($i%$numRows==0 && $supplierWiseDeclaredRecordSize!=$numRows)
									{
										$j++;
							?>
							</table>							</td></tr>
  <tr>
    <td colspan="17"><table width="98%" align="center" cellpadding="3">
      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
  </tr>
<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
</table>
							</td></tr></table>
							<!-- Setting Page Break start Here-->	 						 
							<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
			  				<table width="90%" align="center" cellpadding="0" cellspacing="0" class="boarder">
							 <tr>
							  <td>
							   <table width="100%" align="center" bgcolor="#FFFFFF">
							    <tr>
							      <td colspan="17"><table width="100%">
	    <tr><td>
	  <table width="100%" border="0" align="center">
        <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-head" ><font size="3"><?=$companyArr["Name"];?></font> </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item"><font size="2">WEIGHMENT CHALLAN
      REPORT </font></td>
  </tr>
      </table>
	  </td></tr>
	    <tr>
	      <td height="5"></td>
        </tr>
	    <tr><td>
	  <table width="450" border="0" align="center" cellpadding="0" cellspacing="0">
	  <?
	  $supplierRec	=	$supplierMasterObj->find($selectSupplier);
	  $supplierName	=	$supplierRec[2];
	  ?>
        <tr>
          <td colspan="2" nowrap="nowrap" class="listing-head"><table width="200" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td class="listing-head" nowrap="nowrap">Supplier Name: </td>
          <td class="listing-item" nowrap><?=$supplierName?></td>
            </tr>
	 <?if ($subSupplierChk) {?>
	<tr><TD height="5"></TD></tr>
	  <tr>
              <td class="listing-head" nowrap>Sub-Supplier Name: </td>
              <td class="listing-item" nowrap><?=$subSupplierName?></td>
            </tr>
	<? }?>
          </table></td>
          </tr>

        <tr>
          <td colspan="2" height="5"></td>
        </tr>
        <tr>
          <td colspan="2"><table width="200" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td class="listing-head" nowrap>Supplied From:</td>
              <td class="listing-item" nowrap="nowrap"><?=$dateFrom?></td>
			  <td class="listing-head" nowrap>&nbsp;&nbsp;Till:</td>
              <td class="listing-item" nowrap="nowrap"><?=$dateTill?></td>
            </tr>
          </table></td>
          </tr>
      </table></td></tr>
	  <tr>
	    <td height="5"></td>
	    </tr>
	  </table></td>
						         </tr>
							    <tr>
								 <td colspan="17">
								 	<table width="70%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
							  	<? $columnSize = sizeof($processCountWiseDeclaredRecords);?>


                                  <tr bgcolor="#f2f2f2">
                                    <td colspan="2" class="listing-head">&nbsp;</td>
                                    <td class="listing-head" colspan="<?=$columnSize?>" align="center">Process Code &amp; Decl.Count </td>
                                    <td class="listing-head" align="center">&nbsp;</td>
                                  </tr>
                                  <tr bgcolor="#f2f2f2" align="center">
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier RM Challan Date </th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier RM Challan No </th>
									<?

									foreach($processCountWiseDeclaredRecords as $ssr){
										$processCode 	= $ssr[8];
										$declCount 		= $ssr[6];
									?>
                                    <th class="listing-head">
                                      <table width="50" align="center" class="noBoarder">
                                        <tr >
                                          <td class="listing-head" align="center" bgcolor="#999999"><?=$processCode?></td>
                                        </tr>
                                        <tr>
                                          <td class="listing-head" align="center"><?=$declCount?></td>
                                        </tr>
                                      </table></th>
									  <? }?>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px;">Total.Qty</th>
                                  </tr>

                             <?
							 		}
								 $prevRecDate	=	$sChallanDate;
							 }
							  ?>
								   <tr bgcolor="#FFFFFF">
                                    <td colspan="2" align="right" nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">TOTAL DECL WT:</td>
                                    <?
					$grandTotalDeclWt	= "";
					foreach($processCountWiseDeclaredRecords as $ssr)
					{
						$grandTotalDeclWt = $ssr[5];
						//$declCount 		= $ssr[6];
						//$grandTotalDeclWt = $dailycatchsummaryObj->getGrandTotalWt($declCount);
					?>
                                    <td nowrap class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$grandTotalDeclWt?></td>
									<? }?>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($grandTotalWt,2);?></strong></td>
                                  </tr>
            </table></td>
  </tr>
							    <tr>
							      <td colspan="17"><table width="98%" align="center" cellpadding="3">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
						         </tr>
<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
								<? } else {?>
								<tr bgcolor="white">
                              <td colspan="17"  class="err1" height="5" align="center">
                                <?=$msgNoRecords;?>                              </td>
                            </tr>
								<? }?>
</table>
</td></tr></table>
<? }
if($RMMatrix!=""){
?>
<table width="90%" align="center" cellpadding="0" cellspacing="0" class="boarder">
  <tr><td>
<table width="100%" align="center" bgcolor="#FFFFFF">
	  <?
	   if( sizeof($RMChallanWiseRecords) && $RMMatrix!=""){
	 		$columnSize = sizeof($rmSummaryMatrixRecords);
	?>
	<tr><td colspan="17" align="center" class="fieldName"><font size="2">RM Summary Matrix</font></td></tr>
  <tr>
	<td colspan="17">
	<table width="70%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
                                  <tr bgcolor="#f2f2f2">
                                    <td colspan="2" class="listing-head">&nbsp;</td>
                                    <td class="listing-head" colspan="<?=$columnSize?>" align="center" style="line-height:normal; font-size:10px;">Fish, Process Code &amp; Grade/Count </td>
                                    <td class="listing-head" align="center">&nbsp;</td>
                                  </tr>
                                  <tr bgcolor="#f2f2f2" align="center">
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;"> RM Challan Date </th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;">Wt. Challan No </th>
					<?
					foreach($rmSummaryMatrixRecords as $pcsr)
					{
						$catchEntryId	=	$pcsr[0];
						$fishRec		=	$fishmasterObj->find($pcsr[1]);
						$fishName		=	$fishRec[1];
						$processCodeId		=	$pcsr[2];
						$processCodeRec		=	$processcodeObj->find($processCodeId);
						$processCode	=	$processCodeRec[2];
						$count			=	$pcsr[3];
						$effectiveWt	=	$pcsr[9];
						$totalWt	+=	$effectiveWt;
						$receivedBy	=	$pcsr[8];
						$gradeCode	=	"";
						if($count=="" || $receivedBy=='B' || $receivedBy=='G')
						{
							$gradeRec	=	$grademasterObj->find($pcsr[7]);
							$gradeCode	=	stripSlash($gradeRec[1]);
						}
					?>
                                    <th>
                                      <table width="50" border="0" align="center" class="noBoarder">
                                        <tr>
                                          <td class="listing-head" align="center" style="line-height:normal; font-size:10px;"><?=$processCode?></td>
                                        </tr>
                                        <tr>
                                          <td class="listing-head" align="center" style="line-height:normal; font-size:10px;">
					  <?php
					  if($count=="") echo $gradeCode;
					  else echo $count;							?> </td>
					   </tr>
                                      </table></th>
				  <?
				 	}
				 ?>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;">Total.Qty</th>
                                  </tr>
                                  <?
				 #Finding Total page
				$RMChallanWiseRecordSize = sizeof($RMChallanWiseRecords);
				$totalPage = ceil($RMChallanWiseRecordSize/$numRows);
				$i = 0;
				$j = 0;
				$gradeCode="";
				$totalWt	=	"";
				$prevRecDate = "";
				foreach ($RMChallanWiseRecords as $rmcr) {
								$i++;

								$RMEntryId	=	$rmcr[0];

								$RMChallanNo = $rmcr[13];

								$sChallanDate = $rmcr[1];
								$RMChallanDate = "";
								if($prevRecDate!=$sChallanDate){
									$array			=	explode("-",$sChallanDate);
									$RMChallanDate	=	$array[2]."/".$array[1]."/".$array[0];
								}

								?>
                               <tr bgcolor="#FFFFFF">
                                  <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;"><?=$RMChallanDate?></td>
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;"><?=$RMChallanNo?></td>
				<?
					$effectiveQty = "";
					$totalEffectiveQty = 0;
					$qty = 0;
					foreach($rmSummaryMatrixRecords as $pcsr)
						{
							$entryFishId 	=	$pcsr[1];
							$processCodeId	=	$pcsr[2];
							$count		=	$pcsr[3];		$receivedBy	=	$pcsr[8];
							$gradeCode	=	"";
							$rmGradeId = "";
							if($count=="" || $receivedBy=='B' || $receivedBy=='G')
							{
								$rmGradeId = $pcsr[7];
							}
							#finding Effective wt for each cell
							$effectiveQty = $dailycatchsummaryObj->getEffectiveWt($RMEntryId,$entryFishId, $processCodeId, $count, $rmGradeId);
							$totalEffectiveQty +=$effectiveQty;
				?>
                                    <td class="listing-item" nowrap  align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;"><?=$effectiveQty?></td>
					 <?
						}
					?>
				   <?
					$grandTotalWt+=$totalEffectiveQty;
				  ?>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;"><? echo number_format($totalEffectiveQty,2);?></td>
              </tr>
              <?
	  			if($i%$numRows==0 && $RMChallanWiseRecordSize!=$numRows)
					{
						$j++;
		?>
          </table></td></tr>
  <tr>
    <td colspan="17"><table width="98%" align="center" cellpadding="3">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px;">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
  </tr>
<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
</table></td></tr></table>
									<!-- Setting Page Break start Here-->
								<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
									<table width="90%" align="center" cellpadding="0" cellspacing="0" class="boarder">
									 <tr>
									  <td>
									  <table width="100%" align="center" bgcolor="#FFFFFF">
									    <tr>
									      <td colspan="17" align="center" class="fieldName"><span class="listing-head"><font size="3"><?=$companyArr["Name"];?></font></span></td>
								        </tr>
									    <tr>
									      <td colspan="17" align="center" height="5"></td>
								        </tr>
									    <tr><td colspan="17" align="center" class="fieldName"><font size="2">RM  Summary Matrix</font>- Cont.</td></tr>
									    <tr>
										 <td>
										 	<table width="55%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
											 <tr bgcolor="#f2f2f2">
                                    <td colspan="2" class="listing-head">&nbsp;</td>
                                    <td class="listing-head" colspan="<?=$columnSize?>" align="center" style="line-height:normal; font-size:10px;">Fish, Process Code &amp; Grade/Count </td>
                                    <td class="listing-head" align="center">&nbsp;</td>
                                  </tr>
							  	  <tr bgcolor="#f2f2f2" align="center">
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;"> RM Challan Date </th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;">Wt. Challan No </th>
									<?

									foreach($rmSummaryMatrixRecords as $pcsr){
										$catchEntryId	=	$pcsr[0];

										$fishRec		=	$fishmasterObj->find($pcsr[1]);
										$fishName		=	$fishRec[1];

										$processCodeId	=	$pcsr[2];
										$processCodeRec		=	$processcodeObj->find($processCodeId);
										$processCode	=	$processCodeRec[2];

										$count			=	$pcsr[3];

										$effectiveWt	=	$pcsr[9];
										$totalWt	+=	$effectiveWt;

										$receivedBy	=	$pcsr[8];
										$gradeCode	=	"";
										if($count=="" || $receivedBy=='B' || $receivedBy=='G'){
											$gradeRec			=	$grademasterObj->find($pcsr[7]);
											$gradeCode			=	stripSlash($gradeRec[1]);
										}
									?>
                                    <th>
                                      <table width="50" border="0" align="center" class="noBoarder">

                                        <tr>
                                          <td class="listing-head" align="center" style="line-height:normal; font-size:10px;"><?=$processCode?></td>
                                        </tr>
                                        <tr>
                                          <td class="listing-head" align="center" style="line-height:normal; font-size:10px;">
										  <?
										  if($count=="") echo $gradeCode;
										  else echo $count;
										  ?></td>
									    </tr>
                                      </table></th>
									  <? }?>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;">Total.Qty</th>
                                  </tr>
                                  <?
					}
					$prevRecDate	=	$sChallanDate;
				  }
				  ?>
								   <tr bgcolor="#FFFFFF">
                                    <td colspan="2" align="right" nowrap class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;">TOTAL WT:</td>
                                    <?
					foreach($rmSummaryMatrixRecords as $pcsr)
						{
							$totalColumnEffectiveWt	=	$pcsr[9];
				    ?>
                                    <td nowrap class="listing-item" align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;"><strong><?=$totalColumnEffectiveWt?></strong></td>
						<?
						}
						?>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;"><strong><? echo number_format($grandTotalWt,2);?></strong></td>
                                  </tr>
            </table></td>
  </tr>
									    <tr>
									      <td><table width="98%" align="center" cellpadding="3">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; line-height:normal; font-size:10px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; line-height:normal; font-size:10px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
								        </tr>
	<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
								<? } else {?>
								<tr bgcolor="white">
                              <td colspan="17"  class="err1" height="5" align="center">
                                <?=$msgNoRecords;?></td>
                            </tr>
								<? }?>
</table>
</td></tr></table>
<? }?>
<!-- Local Qty Report-->
<? if ($localQtyReportChk && sizeof($dailyCatchReportRecords)) {?>

  <table align="center">
  <tr bgcolor=white>
    <td colspan="17" align="left">
	<table cellpadding="0" cellspacing="0">
        <tr bgcolor=white>
          <td colspan="17" align="center" height="5"></td>
        </tr>
        <tr bgcolor=white>
          <td colspan="17" align="center" class="fieldName" style="line-height:15px;"><font size="2">Local Quantity Report</font></td>
        </tr>
        <tr bgcolor=white>

          <td colspan="17" align="center" height="5"></td>
        </tr>
      <tr bgcolor=white>
    <td colspan="17" align="center">
	<table class="print">
        <tr bgcolor="#f2f2f2" align="center">
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Date</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">Wt Challan No</th>
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
        <?
	#Finding Total page
	$dailyCatchReportRecordSize = $dailyCatchReportRecords;
	$totalPage = ceil($dailyCatchReportRecordSize/$numRows);
	$i = 0;
	$j = 0;

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
		$selPrevEntryDate = "";
		$enteredDate = "";
		while ($dcr=$dailyCatchReportResultSetObj->getRow()) {
			$i++;

			$catchEntryId		=	$dcr[0];
			$selEntryDate		= 	$dcr[3];
			$enteredDate = "";
			if ($selPrevEntryDate!=$selEntryDate) {	
				$enteredDate	=	dateFormat($selEntryDate);
			}
			$challanNo		=	$dcr[52];
			$WtChallanNumber 	=	"";
			if ($prevChallanNo != $challanNo) {
				$WtChallanNumber = $dcr[52];
			}
			$selFishId			=	$dcr[11];
			$fishName		=	"";
			if ($prevFishId	!= $selFishId) {
				$fishRec	=	$fishmasterObj->find($selFishId);
				$fishName	=	$fishRec[1];
			}

			$processCodeRec		=	$processcodeObj->find($dcr[12]);
			$processCode		=	$processCodeRec[2];

			$selectRate		=	$dcr[33];

			$actualRate		=	$dcr[34];

			$paymentBy	=	$dcr[44];
			$receivedBy	=	$dcr[48];

			$count		=	$dcr[13];
			$countAverage	=	$dcr[14];
			$gradeCode = "";
			if ($count == "" || $receivedBy=='B') {
				$gradeRec		=	$grademasterObj->find($dcr[37]);
				$gradeCode		=	stripSlash($gradeRec[1]);
			}


			$localQty	=	$dcr[16];
			$totalLocalQty += $localQty;

			$wastageQty	=	$dcr[17];
			$totalWastageQty += $wastageQty;

			$softQty	=	$dcr[18];
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

			$effectiveWt	=	$dcr[28];

		$remarks		=	$dcr[23];

		$grandTotalEffectiveWt	+=	$effectiveWt;
		$grandTotalActualAmount += 	$actualRate;
		$grandTotalselectAmount +=$selectRate	
		?>
                <tr bgcolor="#FFFFFF">
		<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="center"><?=$enteredDate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$WtChallanNumber?></td>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt; line-height:normal;"><?=$fishName?></td>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$processCode?></td>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" nowrap="nowrap"><?=$count?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$gradeCode?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$remarks?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><? echo number_format($effectiveWt,2);?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$selectRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$actualRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$adjustWt?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><? echo number_format($adjustWtRate,2,'.','');?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$localQty?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalLocalQtyRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$wastageQty?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalWastageQtyRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$softQty?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalSoftQtyRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalLocalRate,2,'.','');?></strong></td>
      </tr>
	<?
		if($i%$numRows==0 && $dailyCatchReportRecordSize!=$numRows)
		{
			$j++;
	?>
		    </table></td></tr>
			<tr><td><table width="98%" cellpadding="2">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;font-size:10px"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;font-size:10px">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td></tr>
<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
	  </table></td></tr></table>
		<!-- Setting Page Break start Here-->	  
	<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->	
								  <table align="center">
								  <tr>
								    <td colspan="17" class="listing-head" align="center"><font size="3"><?=$companyArr["Name"];?></font></td>
								    </tr>
								  <tr>
								    <td colspan="17" height="5"></td>
								    </tr>
								  <tr>
								  <td colspan="17">
									<table cellpadding="0" cellspacing="0">
									<tr>
									  <td class="fieldName" style="line-height:15px;" align="center"><font size="2">Local Quantity Report</font>- Cont.</td>
									  </tr>
									<tr><td height="5"></td></tr>
									 <tr>
									 <td colspan="17">
	<table class="print">
         <tr bgcolor="#f2f2f2" align="center">
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Date</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">Wt Challan No</th>
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
                 <?
			}
		  	$prevChallanNo = $challanNo;
	  		$prevFishId	= $fishId;
			 $selPrevEntryDate = $selEntryDate;
	  	  }
		 ?>
<tr bgcolor="#FFFFFF">
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-head" align="right" nowrap style="padding-left:2px; padding-right:2px; font-size:7pt;">Total:</td>
	<td height='20' class="listing-item">&nbsp;</td>
	 <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalEffectiveWt,2);?></strong></td>
	 <td height='20' class="listing-item"><strong><? //echo number_format($grandTotalselectAmount,2);?></strong></td>
	
	
        
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalActualAmount,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalAdjustWt,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalAdjstWtRate,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalLocalQty,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalLocalQtyRate,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalWastageQty,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalWastageQtyRate,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($totalSoftQty,2);?></strong></td>
	<td height='20' nowrap="nowrap" class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? echo number_format($grandTotalSoftQtyRate,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;" align="right"><strong><? echo number_format($grandTotalLocalRate,2);?></strong></td>
      </tr>
    </table>
  </td>
    </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr bgcolor="#FFFFFF">
          <td align="center" height="5"></td>
        </tr>
		 <tr><td><table width="98%" align="center" cellpadding="2">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td></tr>
	<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
    </table>

	</td></tr></table>
	</td></tr></table>
	 </td>
  </tr>
</table></td></tr>
</table>
<? }?>
<!--  Advanced Search-->
 <? if($advSearch==true){?>
  <table align="center">
  <tr bgcolor=white>
    <td colspan="17" align="left">
	<table cellpadding="0" cellspacing="0">
       <!-- <tr bgcolor=white>
          <td colspan="17" align="center" height="5"></td>
        </tr>-->
       <!-- <tr bgcolor=white>
          <td colspan="17" align="center" class="fieldName" style="line-height:15px;"><font size="2">Local Quantity Report</font></td>
        </tr>-->
       <!-- <tr bgcolor=white>
          <td colspan="17" align="center" height="5"></td>
        </tr>-->
      <tr bgcolor=white>
    <td colspan="17" align="center">
	<table class="print">
        <tr bgcolor="#f2f2f2" align="center">
	<?

	 if($selectedTableHeader[0]=="Date" || $selectedTableHeader[1]=="Wt Challan No") {
		#Using in Advance and Quick Search
		$dailyCatchSummaryRecords = $dailycatchsummaryObj->filterDailyCatchSummaryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $dateSelectFrom, $billingCompany);
	 } 
	if ($selectedTableHeader[0]!="Date" && $selectedTableHeader[1]!="Wt Challan No") {
		$dailyCatchSummaryRecords = $dailycatchsummaryObj->getAdvanceSearchGroupRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $selectADate, $billingCompany);
	}		

	//if (sizeof($selectedTableHeader)>0) {
		for ($k=0;$k<$arraySize;$k++) {
			 $tHeader	=	$selectedTableHeader[$k];
			 
				
			 if ($tHeader!="") {
	?>
	<th class="listing-head" style="padding-left:3px; padding-right:3px;"><?=$tHeader?></th>
	<? 		}
		}
	?>
      </tr>
        <?
	#Finding Total page
	$dailyCatchReportRecordSize = sizeof($dailyCatchSummaryRecords);
	$totalPage = ceil($dailyCatchReportRecordSize/$numRows);
	$i = 0;
	$j = 0;

 	$totalWt	=	"";
	$grandTotalNetWt	=	"";
	$grandtotalAdjustWt	=	"";
	$totalLocalQty		=	"";
	$totalWastageQty	=	"";
	$totalSoftQty		=	"";
	$totalAdjustWt		=	"";
								  
	foreach($dailyCatchSummaryRecords as $cer) {
		$i++;
		
		$catchEntryId	=	$cer[0];
		$array			=	explode("-",$cer[3]);
		$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
		
		//$weigmentChellanNo	=	$cer[6];
		$weigmentChellanNo	=	$cer[47];
		$supplier		=	$cer[8];
		$supplierRec		=	$supplierMasterObj->find($supplier);
		$supplierName	=	$supplierRec[2];
		
		$fishRec		=	$fishmasterObj->find($cer[11]);
		$fishName		=	$fishRec[1];
		
	
		$local			=	$cer[16];
		$totalLocalQty	+=	$local;
		
		$wastage		=	$cer[17];
		$totalWastageQty += 	$wastage;
			
		$soft		=	$cer[18];
		$totalSoftQty	+=	$soft;
		
		$processCodeId	=	$cer[12];
		$processCodeRec		=	$processcodeObj->find($processCodeId);
		$processCode	=	$processCodeRec[2];
		
		$adjustWt		=	$cer[20];
		$totalAdjustWt		+=	$adjustWt;
	
		$gradeCountAdj		=	$cer[44];
		$totalGradeCountAdj	+= 	$gradeCountAdj;
	
		//$totalAdjustment	=	$adjustWt + $gradeCountAdj;
		//$grandtotalAdjustWt 	+= 	$totalAdjustment;
		
		$reason			=	$cer[19];
		$peeling		=	$cer[22];
		$remarks		=	$cer[23];
		$netWt			=	$cer[26];
		$grandTotalNetWt	+=	$netWt;
		
		$declWt			=	$cer[29];
		$declCount		=	$cer[30];
		
		$dailyRateRec	=	$supplieraccountObj->findDailyRate($cer[11]);
		$declRate		=	$dailyRateRec[7];
		
		$paidStatus		=	$cer[35];
		
		
		
		$selectWeight		=	$cer[32];
		$selectRate			=	$cer[33];
		$actualRate			=	$cer[34];
		
		$payableWt			=	$cer[28];
		$totalWt	+=	$payableWt;	
		
		$payableRate	=	$dailyRateRec[6];
		$receivedBy		=	$cer[46];
		
		$count			=	$cer[13];
		$gradeCode		=	"";
		if($count=="" || $receivedBy=='B' || $receivedBy =='G'){
			$gradeRec			=	$grademasterObj->find($cer[37]);
			$gradeCode			=	stripSlash($gradeRec[1]);
		}
		$centerRec			=	$landingcenterObj->find($cer[7]);
		$landingCenterName	=	stripSlash($centerRec[1]);
		$localReason    = 	$cer[38];
		$wstgeReason 	=	$cer[39];
		$softReason	=	$cer[40];
		$gradeCountAdjReason = 	$cer[45];
		?>
                <tr bgcolor="#FFFFFF">
	<?	
							$tHeader = "";		
							for($k=0;$k<$arraySize;$k++){
								$tHeader	=	$selectedTableHeader[$k];
								if($tHeader!=""){
									$alignStyle = "";
									if ($tHeader=="Effective Wt" || $tHeader=="Decl. Wt" || $tHeader=="Net Wt" || $tHeader=="Adjust Wt" || $tHeader=="Grade/ Count Adj" || $tHeader=="Local" || $tHeader=="Wstge" || $tHeader=="Soft" || $tHeader=="Peeling (%)")  {
										$alignStyle = "align='right'";
									}
							?>
							<td class="listing-item" style="padding-left:3px; padding-right:3px;font-size:12px;" <?=$alignStyle?>>
							<? 
							if($tHeader=="Date") echo $enteredDate;
							if($tHeader=="Wt Challan No") echo $weigmentChellanNo;
							if($tHeader=="Fish") echo $fishName;
							if($tHeader=="Process Code") echo $processCode;
							if($tHeader=="Decl. Ct") echo $declCount;
							if($tHeader=="Decl. Wt") echo $declWt;
							if($tHeader=="Grade") echo $gradeCode;
							if($tHeader=="Count") echo $count;
							if($tHeader=="Net Wt") echo $netWt;
							if($tHeader=="Adjust Wt") echo $adjustWt;
							if($tHeader=="Grade/ Count Adj") echo $gradeCountAdj;
							if($tHeader=="Grade/ Count Adj Reason") echo $gradeCountAdjReason;
							if($tHeader=="Adj Reason") echo $reason;
							if($tHeader=="Local") echo $local;
							if($tHeader=="Local Reason") echo $localReason;
							if($tHeader=="Wstge") echo $wastage;
							if($tHeader=="Wstge Reason") echo $wstgeReason;
							if($tHeader=="Soft") echo $soft;
							if($tHeader=="Soft Reason") echo $softReason;
							if($tHeader=="Peeling (%)") echo $peeling;
							if($tHeader=="Remarks") echo $remarks;
							if($tHeader=="Effective Wt") echo $payableWt;
							
							?>							</td>
							<? 
							}
							}
							?>
      </tr>
	<?
		if($i%$numRows==0 && $dailyCatchReportRecordSize!=$numRows)
		{
			$j++;
	?>
		    </table></td></tr>
			<tr><td><table width="98%" cellpadding="2">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;font-size:12px"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;font-size:12px">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td></tr>
	  </table></td></tr></table>
		<!-- Setting Page Break start Here-->
	<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
								  <table align="center">
								  <tr>
								    <td colspan="17" class="listing-head" align="center"><font size="3"><?=$companyArr["Name"];?></font></td>
								    </tr>
								  <tr>
								    <td colspan="17" height="5"></td>
								    </tr>
								  <tr>
								  <td colspan="17">
									<table cellpadding="0" cellspacing="0">
									<tr>
									  <td class="fieldName" style="line-height:15px;" align="center"><font size="2">WEIGHMENT CHALLAN
      REPORT</font>- Cont.</td>
									  </tr>
									<tr><td height="5"></td></tr>
									 <tr>
									 <td colspan="17">
	<table class="print" align="center">
         <tr bgcolor="#f2f2f2" align="center">
	<?
	for ($k=0;$k<$arraySize;$k++) {
			 $tHeader	=	$selectedTableHeader[$k];
			 
				
			 if ($tHeader!="") {
	?>
	<th class="listing-head" style="padding-left:3px; padding-right:3px;"><?=$tHeader?></th>
	<? 		}
		}
	?>
      </tr>
                 <?
			}
		  	$prevChallanNo = $challanNo;
	  		$prevFishId	= $fishId;
	  	  }
		 ?>
<tr bgcolor="#FFFFFF">
	<?	
							$tHeader = "";						
							for($k=0;$k<$arraySize;$k++)
							{
								$tHeader	=	$selectedTableHeader[$k];
								if($tHeader!=""){
									$alignStyle = "";
									if ($tHeader=="Effective Wt" || $tHeader=="Decl. Wt" || $tHeader=="Net Wt" || $tHeader=="Adjust Wt" || $tHeader=="Grade/ Count Adj" || $tHeader=="Local" || $tHeader=="Wstge" || $tHeader=="Soft" || $tHeader=="Peeling (%)")  {
										$alignStyle = "align='right'";
									}
							?>
								<td class="listing-item" bgcolor="#FFFFFF" <?=$alignStyle?>>
								<?
								if($tHeader=="Net Wt") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($grandTotalNetWt,2)."</strong></span>";
								if($tHeader=="Adjust Wt") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($totalAdjustWt,2)."</strong></span>";
								if($tHeader=="Grade/ Count Adj") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($totalGradeCountAdj,2)."</strong></span>";		
								if($tHeader=="Local") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($totalLocalQty,2)."</strong></span>";
								if($tHeader=="Wstge") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($totalWastageQty,2)."</strong></span>";
								if($tHeader=="Soft") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($totalSoftQty,2)."</strong></span>";
													
								if($tHeader=="Effective Wt") echo "<span style=\"padding-left:3px; padding-right:3px;\"><strong>".number_format($totalWt,2)."</strong></span>";
								
								
								?></td>
							<? }}?>
      </tr>
    </table>
  </td>
    </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr bgcolor="#FFFFFF">
          <td align="center" height="5"></td>
        </tr>
		 <tr><td><table width="98%" align="center" cellpadding="2">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:12px"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:12px">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td></tr>
    </table>

	</td></tr></table>
	</td></tr></table>
	 </td>
  </tr>
</table></td></tr>
</table>
<? }?>
<!--  Advance Search Ends Here-->
<!-- RM Supplier Wise Rate Report -->
<?php
if ($RMRateMatrix!="") {
?>
<table width="90%" align="center" cellpadding="0" cellspacing="0" class="boarder">
  <tr><td>
<table width="100%" align="center" bgcolor="#FFFFFF">
	  <?
	   if (sizeof($rmSummaryMatrixRecords) && $RMRateMatrix!=""){
	 		$columnSize = sizeof($rmSummaryMatrixRecords);
	?>
	<tr><td colspan="17" align="center" class="fieldName"><font size="2">RM Supplier Rate Summary Matrix </font></td></tr>
  <tr>
	<td colspan="17">
	<table width="70%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
                                  <tr bgcolor="#f2f2f2">
                                    <td colspan="4" class="listing-head">&nbsp;</td>
                                    <td class="listing-head" colspan="<?=$columnSize?>" align="center" style="line-height:normal; font-size:10px;">Supplier Rate(s)</td>
                                  </tr>
                                  <tr bgcolor="#f2f2f2" align="center">
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Fish</th>	
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Process</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Grade</th>
				     <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Count</th>	
					<?
				foreach ($rmSupplierRecords as $rsr) {
					$rmSupplierId	= $rsr[0];
					$rmSupplierName = $rsr[1];
				?>
                                    <th>
                                      <table width="50" border="0" align="center" class="noBoarder">
                                        <tr>
                                          <td class="listing-head" align="center" style="line-height:normal; font-size:10px;"><?=$rmSupplierName?></td>
                                        </tr>
                                      </table></th>
				  <?
				 	}
				 ?>
                                  </tr>
                                  <?
				 #Finding Total page
				$RMChallanWiseRecordSize = sizeof($rmSummaryMatrixRecords);
				$totalPage = ceil($RMChallanWiseRecordSize/$numRows);
				$i = 0;
				$j = 0;
				$gradeCode="";
				$totalWt	=	"";
				$prevRecDate = "";
				foreach ($rmSummaryMatrixRecords as $pcsr) {
					$i++;
					$catchEntryId	=	$pcsr[0];
					$rmFishId	= $pcsr[1];
					$fishRec	=	$fishmasterObj->find($rmFishId);
					$fishName		=	$fishRec[1];
	
					$processCodeId	=	$pcsr[2];
					$processCodeRec	=	$processcodeObj->find($processCodeId);
					$processCode	=	$processCodeRec[2];
										
					$count		=	$pcsr[3];
											
					$effectiveWt	=	$pcsr[9];
					$totalWt	+=	$effectiveWt;
										
					$receivedBy	=	$pcsr[8];
					$gradeCode	=	"";
					$rmGradeId = "";
					if ($count=="" || $receivedBy=='B' || $receivedBy=='G') {
							$rmGradeId = $pcsr[7];
							$gradeRec = $grademasterObj->find($pcsr[7]);
							$gradeCode = stripSlash($gradeRec[1]);
					}
				?>

								
                               <tr bgcolor="#FFFFFF">
                                  <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;"><?=$fishName?></td>
                                   <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;"><?=$processCode?></td>			
                                  <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;"><?=$gradeCode?></td>
                                   <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;"><?=$count?></td>	
					
				<?php
					foreach ($rmSupplierRecords as $rs) {					
						$rmSupId	= $rs[0];
						# Get Distinct Rates
						$distinctRMRates = $dailycatchsummaryObj->getRMSupplierRates($fromDate, $tillDate, $selectADate, $dateSelectFrom, $rmFishId, $processCodeId, $count, $rmGradeId, $rmSupId);
				?>
                                    <td class="listing-item" nowrap  align="right" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px;">
						<table border="0" class="nullBorder">
						<tr>
						<?
							$numLine = 3;
							if (sizeof($distinctRMRates)>0) {
								$nextRec	=	0;
								//$k=0;
								$selRate = "";
								foreach ($distinctRMRates as $dR) {
									//$j++;
									$selRate = $dR[0];
									$nextRec++;
						?>
						<td class="listing-item" style="line-height:normal; font-size:11px;">
							<? if($nextRec>1) echo ",";?><?=$selRate?></td>
							<? if($nextRec%$numLine == 0) { ?>
						</tr>
						<tr>
						<? 
								}	
							}
							}
						?>
						</tr>
					</table>
				   </td>
					 <?
						}
					?>
              </tr>
              <?
	  			if($i%$numRows==0 && $RMChallanWiseRecordSize!=$numRows)
					{
						$j++;
		?>
          </table></td></tr>
  <tr>
    <td colspan="17"><table width="98%" align="center" cellpadding="3">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px;">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
  </tr>
</table></td></tr></table>
									<!-- Setting Page Break start Here-->						 
								<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
									<table width="90%" align="center" cellpadding="0" cellspacing="0" class="boarder">
									 <tr>
									  <td>
									  <table width="100%" align="center" bgcolor="#FFFFFF">
									    <tr>
									      <td colspan="17" align="center" class="fieldName"><span class="listing-head"><font size="3"><?=$companyArr["Name"];?></font></span></td>
								        </tr>
									    <tr>
									      <td colspan="17" align="center" height="5"></td>
								        </tr>
									    <tr><td colspan="17" align="center" class="fieldName"><font size="2">RM Supplier Rate Summary Matrix</font>- Cont.</td></tr>
									    <tr>
										 <td>
			<table width="55%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
				 <tr bgcolor="#f2f2f2">
                                    <td colspan="4" class="listing-head">&nbsp;</td>
                                    <td class="listing-head" colspan="<?=$columnSize?>" align="center" style="line-height:normal; font-size:10px;">Supplier Rate(s)</td>
                                  </tr>
                                  <tr bgcolor="#f2f2f2" align="center">
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Fish</th>	
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Process</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Grade</th>
				     <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px;">Count</th>	
					<?
				foreach ($rmSupplierRecords as $rsr) {
					$rmSupplierId	= $rsr[0];
					$rmSupplierName = $rsr[1];
				?>
                                    <th>
                                      <table width="50" border="0" align="center" class="noBoarder">
                                        <tr>
                                          <td class="listing-head" align="center" style="line-height:normal; font-size:10px;"><?=$rmSupplierName?></td>
                                        </tr>
                                      </table></th>
				  <?
				 	}
				 ?>
                                  </tr>
                                  <?
					}					
				  }
				  ?>
								   
            </table></td>
  </tr>
    <tr>
	      <td><table width="98%" align="center" cellpadding="3">
      <tr>
        <td colspan="6" height="10"></td>
        </tr>
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; line-height:normal; font-size:10px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; line-height:normal; font-size:10px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
								        </tr>
								<? } else {?>
								<tr bgcolor="white">
                              <td colspan="17"  class="err1" height="5" align="center">
                                <?=$msgNoRecords;?></td>
                            </tr>
								<? }?>
</table>
</td></tr></table>
<? 
	}
?>
<!-- RM Supplier Rate Summary Matrix -->

<!-- Qty Summary Report -->
<?php
if ($qtySummary!="") {
?>
<table width="90%" align="center" cellpadding="0" cellspacing="0" class="boarder">
  <tr><td>
<table width="100%" align="center" bgcolor="#FFFFFF">
	  <?php
	   if (sizeof($wtChallanQtySummaryRecs) && $qtySummary!=""){	 		
	?>
	<tr><td colspan="17" align="center" class="fieldName"><font size="2">Quantity Summary Report </font></td></tr>
  <tr>
	<td colspan="17">
	<table width="50%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
                                  <tr bgcolor="#f2f2f2" align="center">
                                    <th class="listing-head" style="padding-left:10px; padding-right:10px;">Date</th>	
                                    <th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Challan No</th>
                                    <th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Effective Qty</th>
                                  </tr>
                                  <?
				 #Finding Total page
				$wtChallanQtySummaryRecSize = sizeof($wtChallanQtySummaryRecs);
				$totalPage = ceil($wtChallanQtySummaryRecSize/$qtySumryNumRows);
				$i = 0;
				$j = 0;
				$totalEffectiveQty = "";
				$wtChallanNo 	= "";
				$effectiveQty 	= "";
				foreach ($wtChallanQtySummaryRecs as $wcqr) {
					$i++;
					$challanDate = dateFormat($wcqr[1]);
					$wtChallanNo = $wcqr[4];
					$effectiveQty	= $wcqr[3];
					$totalEffectiveQty += $effectiveQty;
				?>

								
                               <tr bgcolor="#FFFFFF">
                                  <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$challanDate?></td>
                                   <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?=$wtChallanNo?></td>			
                                  <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><? echo number_format($effectiveQty,2,'.',',');?></td>
              </tr>
              <?
	  			if($i%$qtySumryNumRows==0 && $wtChallanQtySummaryRecSize!=$qtySumryNumRows)
					{
						$j++;
		?>
          </table></td></tr>
  <tr>
    <td colspan="17"><table width="98%" align="center" cellpadding="3">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px;">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
  </tr>
<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
</table></td></tr></table>
									<!-- Setting Page Break start Here-->
								<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
									<table width="90%" align="center" cellpadding="0" cellspacing="0" class="boarder">
									 <tr>
									  <td>
									  <table width="100%" align="center" bgcolor="#FFFFFF">
									    <tr>
									      <td colspan="17" align="center" class="fieldName"><span class="listing-head"><font size="3"><?=$companyArr["Name"];?></font></span></td>
								        </tr>
									    <tr>
									      <td colspan="17" align="center" height="5"></td>
								        </tr>
									    <tr><td colspan="17" align="center" class="fieldName">
										<font size="2">Quantity Summary Report</font>- Cont.
										</td></tr>
									    <tr>
										 <td>
			<table width="55%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">				
                                  <tr bgcolor="#f2f2f2" align="center">
                                     <th class="listing-head" style="padding-left:10px; padding-right:10px;">Date</th>	
                                    <th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Challan No</th>
                                    <th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Effective Qty</th>
                                  </tr>
                                  <?
					}					
				  }
				  ?>
			<tr bgcolor="WHITE">
				<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" align="right" colspan="2">Total:</td>				
				<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><strong><? echo number_format($totalEffectiveQty,2,'.',',');?></strong></td>	
			</tr>
								   
            </table></td>
  </tr>
    <tr>
	      <td><table width="98%" align="center" cellpadding="3">
      <tr>
        <td colspan="6" height="10"></td>
        </tr>
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; line-height:normal; font-size:10px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:6px; line-height:normal; font-size:10px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
	        </tr>
<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
								<? } else {?>
								<tr bgcolor="white">
                              <td colspan="17"  class="err1" height="5" align="center">
                                <?=$msgNoRecords;?></td>
                            </tr>
								<? }?>
</table>
</td></tr></table>
<? 
	}
?>
<!-- Qty Summary Ends Here -->

<!-- Challan Summary View Report -->
<?php
if ($challanSummary!="") {
?>
<table width="90%" align="center" cellpadding="0" cellspacing="0" class="boarder">
  <tr><td>
<table width="100%" align="center" bgcolor="#FFFFFF">
	  <?php
	   if (sizeof($challanQtyWiseSummaryRecs) && $challanSummary!=""){	 		
	?>
	<tr><td colspan="17" align="center" class="fieldName"><font size="2">Challan Summary Report </font></td></tr>
  <tr>
	<td colspan="17">
	<table width="50%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
                                  <tr bgcolor="#f2f2f2" align="center">
                                   	 <th class="listing-head" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;">Date</th>
					<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;">Challan No</th>
					<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;">Supplier</th>	
					<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;">Effective Qty</th>	
					<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;">Rate</th>	
					<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;">Amt</th>
                                  </tr>
                                  <?
				 #Finding Total page
				$cSumryViewNumRows = 33;
				$challanQtyWiseSummaryRecSize = sizeof($challanQtyWiseSummaryRecs);
				$totalPage = ceil($challanQtyWiseSummaryRecSize/$cSumryViewNumRows);
				$i = 0;
				$j = 0;
				$totalEffectiveQty = "";
				$wtChallanNo 	= "";
				$effectiveQty 	= "";
				$totSupActualAmt = 0;
				foreach ($challanQtyWiseSummaryRecs as $wcqr) {
					$i++;
					$challanDate = dateFormat($wcqr[1]);
					$wtChallanNo = $wcqr[4];
					$effectiveQty	= $wcqr[3];
					$totalEffectiveQty += $effectiveQty;
					$selSupplierName  = $wcqr[6];
					$supplierActualAmt = $wcqr[5];
					$totSupActualAmt += $supplierActualAmt;
					$rateCSV=$wcqr[7];
					$netrateCSV+=$rateCSV;
				?>

								
                               <tr bgcolor="#FFFFFF">
                                 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;"><?=$challanDate;?></td>
				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;" align="center"><?=$wtChallanNo?></td>
				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;" align="left"><?=$selSupplierName?></td>
				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;" align="right"><? echo number_format($effectiveQty,2,'.',',');?></td>
				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;" align="right"><? echo number_format($rateCSV,2,'.',',');?></td>
				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;" align="right"><? echo number_format($supplierActualAmt,2,'.',',');?></td>
             		  </tr>
              <?
	  			if($i%$cSumryViewNumRows==0 && $challanQtyWiseSummaryRecSize!=$cSumryViewNumRows)
					{
						$j++;
		?>
          </table></td></tr>
  <tr>
    <td colspan="17"><table width="98%" align="center" cellpadding="3">

      <tr>
        <td colspan="6" height="10"></td>
        </tr>

      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; font-size:10px;">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
  </tr>
<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
</table></td></tr></table>
									<!-- Setting Page Break start Here-->
								<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
									<table width="90%" align="center" cellpadding="0" cellspacing="0" class="boarder">
									 <tr>
									  <td>
									  <table width="100%" align="center" bgcolor="#FFFFFF">
									    <tr>
									      <td colspan="17" align="center" class="fieldName"><span class="listing-head"><font size="3"><?=$companyArr["Name"];?></font></span></td>
								        </tr>
									    <tr>
									      <td colspan="17" align="center" height="5"></td>
								        </tr>
									    <tr><td colspan="17" align="center" class="fieldName">
										<font size="2">Challan Summary Report</font>- Cont.
										</td></tr>
									    <tr>
										 <td>
			<table width="55%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">				
                                  <tr bgcolor="#f2f2f2" align="center">
                                     	<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;">Date</th>
					<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;">Challan No</th>
					<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;">Supplier</th>	
					<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;">Effective Qty</th>	
						<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;">Rate</th>	
					<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;">Amt</th>
                                  </tr>
                                  <?
					}					
				  }
				  ?>
			<tr bgcolor="WHITE">
				<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;" align="right" colspan="3">Total:</td>				
				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;" align="right"><strong><? echo number_format($totalEffectiveQty,2,'.',',');?></strong></td>
				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;" align="right"><strong><? //echo number_format($netrateCSV,2,'.',',');?></strong></td>
				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; font-size:11px;" align="right"><strong><? echo number_format($totSupActualAmt,2,'.',',');?></strong></td>
			</tr>
								   
            </table></td>
  </tr>
    <tr>
	      <td><table width="98%" align="center" cellpadding="3">
      <tr>
        <td colspan="6" height="10"></td>
        </tr>
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px; line-height:normal; font-size:10px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:6px; line-height:normal; font-size:10px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
        </tr>
	<tr bgcolor="#FFFFFF">
	<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
		<tr>
		<td colspan="6" height="20"></td>
		</tr>		
			<tr><TD colspan="6" height="5"></TD></tr>
			<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
		</table>
	</td>
 </tr>
								<? } else {?>
								<tr bgcolor="white">
                              <td colspan="17"  class="err1" height="5" align="center">
                                <?=$msgNoRecords;?></td>
                            </tr>
								<? }?>
</table>
</td></tr></table>
<? 
	}
?>
<!--  Challan Summary Ends Here-->



<!--<SCRIPT LANGUAGE="JavaScript">	
	window.print();	
</SCRIPT>-->
</body></html>