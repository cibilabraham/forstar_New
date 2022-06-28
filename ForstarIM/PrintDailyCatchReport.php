<?php
	require("include/include.php");

	# -------------------- Billing Company started -------
	$challanMainId	= $g["challanMainId"];

	//$billingCompanyId = $g["billingCompany"];	
	list($billingCompanyId, $selWeighmentNo, $alphaCode) = $dailycatchreportObj->getBillCompanyRecId($challanMainId);
	if ($billingCompanyId>0) {	# Getting Rec from other billing company
		list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $billingCompanyObj->getBillingCompanyRec($billingCompanyId);
	} else {	# Getting Rec from Company Details Rec
		list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $companydetailsObj->getForstarCompanyDetails();
	}	
	$displayAddress		= "";
	$displayTelNo		= "";
	if ($companyName)	$displayAddress = $address."&nbsp;".$place."&nbsp;".$pinCode;
	if ($telNo)		$displayTelNo	= $telNo;
	if ($faxNo)		$displayTelNo	.= "&nbsp;/&nbsp;".$faxNo;
	//echo $companyName."<br>".$displayAddress."<br>".$displayTelNo;
	# -------------------- Billing Company Ends Here -------

	if ($g["selWeighment"]=="") {
		$selWeighmentNo		= $g["weighNumber"];
		//$dailyCatchReport	= $dailycatchreportObj ->fetchAllCatchReportRecords($challanMainId);
		$dailyCatchReportRecords = $dailycatchreportObj ->fetchAllCatchReportRecords($challanMainId);
		$dailyCatchReport =  $dailyCatchReportRecords;
	} else {
		//$selWeighmentNo		= $g["selWeighment"];
		$fromDate		=	$g["fromDate"];	
		$tillDate		=	$g["tillDate"];
		//$dailyCatchReport 	= $dailycatchreportObj->fetchAlldailyCatchReportRecords($challanMainId, $fromDate, $tillDate);
		$dailyCatchReportRecords = $dailycatchreportObj->fetchAlldailyCatchReportRecords($challanMainId, $fromDate, $tillDate);
		$dailyCatchReport =  $dailyCatchReportRecords;
	}
		
	if (sizeof($dailyCatchReport)>0) {
	
		// Finding Supplier Record
		$supplierRec	=	$supplierMasterObj->find($dailyCatchReport[0][8]);
		$supplierName	=	$supplierRec[2];
		//Finding Landing Center Record
		$centerRec			=	$landingcenterObj->find($dailyCatchReport[0][7]);
		$landingCenterName		=	stripSlash($centerRec[1]);
		// Finding Plant Record
		$plantRec			=	$plantandunitObj->find($dailyCatchReport[0][1]);
		$plantName			=	stripSlash($plantRec[2]);

		$Date1			=	explode("-",$dailyCatchReport[0][3]); //2007-06-27
		$date			= 	date("j M Y", mktime(0, 0, 0, $Date1[1], $Date1[2], $Date1[0]));
		
		$selectTime		=	explode("-",$dailyCatchReport[0][43]);
		$time			=	$selectTime[0].":".$selectTime[1]."&nbsp;".$selectTime[2];	
		$vechNo			=	$dailyCatchReport[0][4];		
	}

	/*
		foreach($dailyCatchReport as $dcrs) {
		// Finding Supplier Record
		$supplierRec	=	$supplierMasterObj->find($dcrs[8]);
		$supplierName	=	$supplierRec[2];
		//Finding Landing Center Record
		$centerRec			=	$landingcenterObj->find($dcrs[7]);
		$landingCenterName		=	stripSlash($centerRec[1]);
		// Finding Plant Record
		$plantRec			=	$plantandunitObj->find($dcrs[1]);
		$plantName			=	stripSlash($plantRec[2]);

		$Date1			=	explode("-",$dcrs[3]); //2007-06-27
		$date			= 	date("j M Y", mktime(0, 0, 0, $Date1[1], $Date1[2], $Date1[0]));
		
		$selectTime		=	explode("-",$dcrs[43]);
		$time			=	$selectTime[0].":".$selectTime[1]."&nbsp;".$selectTime[2];	
		$vechNo			=	$dcrs[4];		
	}
	*/
	
?>
<html>
<title>DAILY WEIGHMENT REPORT</title>
<head>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
	function hidePrintButton()
	{
		document.getElementById("printButton").style.display="block";	
	}
	function printDoc()
	{
		window.print();	
		return false;
	}
	
	function printThisPage(printbtn)
	{
		document.getElementById("printButton").style.display="none";	
		if (!printDoc()) {			
			setTimeout("hidePrintButton()",2000);				
		}		
	}	
</script>
</head>
<body>
<table width="95%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 
  <tr bgcolor="white">
	<TD colspan="17" align="center">
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr bgcolor=white>
				<td class="listing-head" align="center" ><font size="5"><?=$companyName?></font></td>
			</tr>
			<tr bgcolor=white>
				<td align="LEFT" class="listing-head" ></td>
			</tr>
			<tr bgcolor=white>
				<td class="listing-item" align="center"><?=$displayAddress?></td>
			</tr>
			<tr bgcolor=white>
				<td class="listing-item" align="center" >Tel: <?=$displayTelNo?></td>
			</tr>
		</table>
	</TD>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center">
		<table width='100%' bgcolor="#f2f2f2">
			<tr>
			<td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
				<font size="3">DAILY WEIGHMENT REPORT</font>
			</td>
			</tr>
		</table>
    </td>
  </tr>	
<!--  <tr bgcolor=white> 
    <td colspan="17" align="LEFT" class="listing-item"><font size="2">DAILY WEIGHMENT 
      REPORT </font></td>
  </tr>-->
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="center">
	<table width="100%" cellpadding="0" cellspacing="0" class="print">
	<tr>
	<td>
	<table width="100%" cellpadding="0" cellspacing="0" class="tdBoarder">
        <tr> 
          <td class="fieldName">
		  <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName">Supplier:</td>
                <td class="listing-item"><?=$supplierName?>&nbsp;</td>
              </tr>
            </table></td>
          <td class="listing-item"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap="nowrap">Landing Center:</td>
                <td class="listing-item" nowrap><?=$landingCenterName?>&nbsp;</td>
              </tr>
            </table></td>
          <td class="fieldName"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap>Wt Challan No:</td>
                <td class="listing-item"><?=$alphaCode.$selWeighmentNo?></td>
              </tr>
            </table></td>
          <td class="listing-item">&nbsp; </td>
        </tr>
        <tr> 
          <td class="fieldName">
		  <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap>Supplied At:</td>
                <td class="listing-item" nowrap><?=$plantName?></td>
              </tr>
            </table>		  </td>
          <td class="listing-item"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName">Date/Time:</td>
                <td class="listing-item"><?=$date?>-<?=$time?>&nbsp;</td>
              </tr>
            </table></td>
          <td class="fieldName"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap>Vehicle No:</td>
                <td class="listing-item" nowrap>&nbsp;<?=$vechNo?></td>
              </tr>
            </table></td>
          <td class="listing-item"></td>
        </tr>
      </table>	  </td></tr></table>	  </td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"> 
 	<?
		if( sizeof($dailyCatchReportRecords)){
		$i	=	0;
	?>
      <?
	foreach($dailyCatchReportRecords as $dcr){
	$i++;
	
	$catchEntryId	=	$dcr[0];
	$array			=	explode("-",$dcr[3]);
	$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
	
		
	$fishId			=	$dcr[11];
	
	$fishRec		=	$fishmasterObj->find($fishId);
	$fishName		=	$fishRec[1];
	
	$count			=	$dcr[13];
	$countAverage		=	$dcr[14];
	
	$processCodeRec		=	$processcodeObj->find($dcr[12]);
	$processCode		=	$processCodeRec[2];
	
	$netWt			=	$dcr[27];
	
	$declWt			=	$dcr[29];
	$declCount		=	$dcr[30];
	
	$dailyRateRec		=	$supplieraccountObj->findDailyRate($fishId);
	
	$declRate		=	$dailyRateRec[7];
	
	$paidStatus		=	$dcr[35];
	
		
	$selectWeight		=	$dcr[32];
	$selectRate		=	$dcr[33];
	$actualRate		=	$dcr[34];
	
			
	$gradeRec		=	$grademasterObj->find($dcr[37]);
	$gradeCode		=	stripSlash($gradeRec[1]);
	
	$dailyCatchEntryId	=	$dcr[42];
	
# -- count all Gross Records -------------------------------------------------------
	$countGrossRecords	=	$dailycatchentryObj->fetchAllGrossRecords($dailyCatchEntryId);

	$totalWt	=	"";
	$grandTotalBasketWt = "";
	$netGrossWt	=	"";
		foreach ($countGrossRecords as $cgr)
		{
					$countGrossWt			=	$cgr[1];
					$totalWt				=	$totalWt+$countGrossWt;
					$countGrossBasketWt		=	$cgr[2];
					$grandTotalBasketWt		=	$grandTotalBasketWt + $countGrossBasketWt;
					$netGrossWt				=	$totalWt - $grandTotalBasketWt;
		}

		//In the case of Net Weight Entry
		//echo $entryOption	=	$dcr[41];
		if($dcr[41]=='N'){
			$netGrossWt	=	$dcr[26];
		}
		
		$localQty		=	$dcr[16];
		$wastageQty		=	$dcr[17];
		$softQty		=	$dcr[18];
		$gradeCountAdj		=	$dcr[46];
		
		//$adjustWt		=	$dcr[20] + $localQty + $wastageQty + $softQty + $gradeCountAdj; edited 14-01-08 //Removed +  $gradeCountAdj
		$adjustWt		=	$dcr[20];	

		$otherAdjustWt		=	$localQty + $wastageQty + $softQty ;
		
		$totalAdjustWt 		=	$adjustWt + $otherAdjustWt + $gradeCountAdj;

		$effectiveWt	=	$netGrossWt - $totalAdjustWt;

		$totEffectiveWt +=	$effectiveWt; //For Total Effective Weight 

		$goodPacking	=	$dcr[21];
		$goodPackQty	=	($effectiveWt*$goodPacking)/100;
		
		$forPeeling	=	$dcr[22];
		$goodPeelQty	=	($effectiveWt*$forPeeling)/100;
		
		$remarks	=	$dcr[23];
		
		$paymentBy	=	$dcr[44];
		$receivedBy	=	$dcr[48];

		$adjustQtyReason = 	$dcr[19];
		$localQtyReason	 = 	$dcr[38];
		$wastageQtyReason = 	$dcr[39];
		$softQtyReason	  =	$dcr[40];	
		$gradeCountAdjReason = 	$dcr[47];
		
#--------------------------------------------------------------------------
	if($effectiveWt!=0) {
	?>
      <table>
        <tr>
          <td height="5"></td>
        </tr>
      </table>
      <table width="100%" border="0" cellpadding="3" cellspacing="0" style="border:1px solid;">
        <tr bgcolor="#FFFFFF"> 
          <td align="center"> <table width="100%" cellpadding="0" cellspacing="0">
              <tr> 
                <td colspan="9" valign="top" class="fieldName"><table>
                    <tr> 
                      <td class="fieldName">Fish:</td>
                      <td class="listing-item">
                        <?=$fishName?>                      </td>
                    </tr>
                  </table></td>
                <td valign="top" class="fieldName">
				<table>
                    <tr> 
                      <td class="listing-item">
                        <?=$processCode?>                      </td>
                      <td class="fieldName">&nbsp;</td>
                    </tr>
                  </table></td>
                <td class="fieldName"> <table width="200" cellpadding="0" cellspacing="0">
                    <? if($count || $receivedBy=='C' || $receivedBy=='B'){?>
                    <tr> 
                      <td class="fieldName">Count:</td>
                      <td class="listing-item"> 
                        <?=$count?>                      </td>
                      <td class="fieldName">Average:</td>
                      <td class="listing-item"> 
                        <?=$countAverage?>                      </td>
                    </tr>
                    <? } 
						if($count=="" || $receivedBy=='G' || $receivedBy=='B') 
						{
					?>
                    <tr> 
                      <td class="fieldName">Grade:</td>
                      <td class="listing-item"> 
                        <?=$gradeCode?>                      </td>
                      <td class="listing-item">&nbsp;</td>
                      <td class="listing-item">&nbsp;</td>
                    </tr>
                    <? }?>
                  </table></td>
              </tr>
            </table></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td align="center"> <table cellpadding="0" cellspacing="0" border="0">
              <tr> 
                <? 
		if(sizeof($countGrossRecords) && $dcr[41]=='B'){
		$col=4;
		for($i=1;$i<=$col;$i++)
			{
		?>
                <td width="9%"> <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#999999" class="print">
                    <tr bgcolor="#f2f2f2" class="listing-head"> 
                      <th width="30%" align="center">No</th>
                      <th align="center" width="40%">Gross</th>
                      <th align="center" width="30%">Net</th>
                    </tr>
                    <? 
					$row=sizeof($countGrossRecords);
				
					$size 	=	ceil($row/$col);
					for($j=1;$j<=$size;$j++)
						{
						$id	=(($i-1)*$size)+$j;
						
		
						
						$hidId="";
						$gwt="";
						$bwt="";
						$netCountWt="";
						
			
				if ( $id <= sizeof($countGrossRecords) )	{
					$rec = $countGrossRecords[$id-1];
					//$hidId=$rec[0];
					$gwt=$rec[1];
					$bwt=$rec[2];
					$netCountWt	=	$gwt-$bwt;
					
				}	
						
						
					?>
                    <tr bgcolor="#FFFFFF"> 
                      <td nowrap class="listing-item" align="center">
                        <? if($gwt=="") { echo 0;} else { echo $id;}?>
                        &nbsp;&nbsp;</td>
                      <td class="listing-item" align="right"><? echo number_format($gwt,2);?>&nbsp;&nbsp;&nbsp;</td>
                      <td class="listing-item" align="right"><? echo number_format($netCountWt,2);?>&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                    <? }?>
                  </table></td>
                <? }
				}
				?>
              </tr>
            </table></td>
        </tr>
        <tr bgcolor=white> 
          <td align="center" class="listing-head"><table width="100%" cellpadding="0" cellspacing="0">
              <tr> 
                <td valign="top"><table width="100%" cellpadding="0" cellspacing="0">
				<? if(sizeof($countGrossRecords) && $dcr[41]=='B'){?>
                    <tr> 
                      <td class="fieldName">Total Wt: </td>
                      <td class="listing-item" align="right"><? echo number_format($totalWt,2);?></td>
                    </tr>
                    <tr> 
                      <td class="fieldName">BKt Wt: </td>
                      <td class="listing-item" align="right"><? echo number_format($grandTotalBasketWt,2);?></td>
                    </tr>
					<? }?>
                    <tr> 
                      <td class="fieldName">Net Wt: </td>
                      <td class="listing-item" align="right"><? echo number_format($netGrossWt,2);?></td>
                    </tr>
		<tr>
            <td class="fieldName">Adjustment:</td>
            <td class="listing-item" align="right"><? echo number_format($adjustWt,2);?></td>
		<? if($adjustWt!=0) {?>
	    <td>&nbsp;</td>
	    <td class="fieldName">Reason:</td>
	    <td class="listing-item"><?=$adjustQtyReason?></td>
	<? }?>
          </tr>
            <tr>
        	<TD class="fieldName">Grade/Count Adj</TD>
                <TD class="listing-item" align="right"><? echo number_format($gradeCountAdj,2);?></TD>
		<? if($gradeCountAdj!=0) {?>
		<td>&nbsp;</td>
	    	<td class="fieldName">Reason:</td>
	    	<td class="listing-item"><?=$gradeCountAdjReason?></td>
		<? }?>
            </tr>
              <tr>
               <TD class="fieldName">Local Qty:</TD>
               <TD class="listing-item" align="right"><?=$localQty?></TD>
		<? if($localQty!=0) {?>
		<td>&nbsp;</td>
	    <td class="fieldName">Reason:</td>
	    <td class="listing-item"><?=$localQtyReason?></td>
		<? }?>
          </tr>
           <tr>
               <TD class="fieldName">Wastage Qty:</TD>
                <TD class="listing-item" align="right"><?=$wastageQty?></TD>
		<? if($wastageQty!=0) {?>
		<td>&nbsp;</td>
	    <td class="fieldName">Reason:</td>
	    <td class="listing-item"><?=$wastageQtyReason?></td>
		<? }?>
           </tr>
           <tr>
           	 <TD class="fieldName">Soft Qty:</TD>
                  <TD class="listing-item" align="right"><?=$softQty?></TD>
		<? if($softQty!=0) { ?>
		<td>&nbsp;</td>
	    <td class="fieldName">Reason:</td>
	    <td class="listing-item"><?=$softQtyReason?></td>
		<? }?>
           </tr>
                  </table></td>
                <td valign="top" align="center"><table width="100" align="center" cellpadding="0" cellspacing="0">
                    <tr> 
                      <td width="117">&nbsp;</td>
                      <td width="62">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr> 
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr> 
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <!--tr> 
                      <td class="fieldName">&nbsp;&nbsp;&nbsp;<strong>Effective</strong>:                      </td>
                      <td class="listing-item"><strong><? echo number_format($effectiveWt,2);?></strong></td>
                    </tr-->
                  </table></td>
                <td valign="top" align="center"><table width="300" cellpadding="0" cellspacing="0">
                    <tr> 
                      <td class="fieldName">Good For PKg :</td>
                      <td class="listing-item" align="right">
                        <?=$goodPacking?>
                        %</td>
                      <td class="fieldName">&nbsp;Qty:</td>
                      <td class="listing-item" align="right"><? echo number_format($goodPackQty,2);?></td>
                    </tr>
                    <tr> 
                      <td class="fieldName">For Peeling :</td>
                      <td class="listing-item" align="right">
                        <?=$forPeeling?>
                        %</td>
                      <td class="fieldName">&nbsp;Qty:</td>
                      <td class="listing-item" align="right"><? echo number_format($goodPeelQty,2);?></td>
                    </tr>
                    <tr> 
                      <td colspan="4"><table width="200" cellpadding="0" cellspacing="0">
              <tr>
                <td class="fieldName">Decl Wt: </td>
                <td class="listing-item"><?=$declWt;?></td>
				<td class="fieldName">Decl Ct: </td>
                <td class="listing-item"><?=$declCount?></td>
              </tr>
            </table></td>
                    </tr>
                    <tr> 
                      <td class="fieldName">Remarks:</td>
                      <td colspan="3" class="listing-item" align="left"><?=$remarks?></td>
                    </tr>
                    <tr> 
                      <td class="fieldName"></td>
                      <td colspan="3" class="listing-item" align="left">&nbsp;</td>
                    </tr>
                  </table></td>
                <td>&nbsp;</td>
              </tr>
            </table></td>
        </tr>
	<tr><TD align="center"><table cellpadding="0" cellspacing="0"><TR><td class="fieldName" nowrap><strong>Effective Wt:</strong> </td>
            <td class="listing-item" nowrap>&nbsp;&nbsp;<strong><? echo number_format($effectiveWt,2);?></strong></td></TR></table></TD></tr>
        <? 
		} // Effective wt Zero loop ending here
	}
	
	?>
        <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
      </table></td>
  </tr>
  <tr bgcolor="#FFFFFF">
		  <td colspan="17">&nbsp;</td>
  </tr>
		<tr bgcolor="#FFFFFF">
		<td colspan="17"><table width="200" align="center">
   <tr>
     <td class="fieldName" nowrap="nowrap"><strong>Total Effective Weight:</strong></td>
     <td class="listing-item" nowrap="nowrap"><strong>&nbsp;<? echo number_format($totEffectiveWt,2);?>&nbsp;Kg</strong></td>
   </tr>
 </table></td></tr>
		<? } else {?>
        <tr bgcolor="white"> 
          <td  class="err1" height="5" align="center" colspan="17">
            <?=$msgNoRecords;?>          </td>
        </tr>
        <? 
			} 
	    ?>
 </table>
 <SCRIPT LANGUAGE="JavaScript">
	<!--
	//window.print();
	//-->
	</SCRIPT>
</body></html>
