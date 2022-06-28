<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$checked	= "";
	$accountSettled = false;
	
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
	$companySpecific = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();	
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	if ($accesscontrolObj->canCompanySpecific()) $companySpecific=true;	

	list($AModuleId,$AFunctionId) = $modulemanagerObj->resolveIds("TransporterRateMaster.php");
	$assignDktNoAccess = $accesscontrolObj->getAccessControl($AModuleId, $AFunctionId);
	$assignDktNoAccess = $accesscontrolObj->canAccess();
	//----------------------------------------------------------


	
	# Update
	if ($p["cmdSaveTransporterAccount"]!="") {	
		$selTransporter		= $p["transporter"];
		$rowCount		= $p["hidRowCount"];
		$billType 		= $p["billType"]; // OD, OC
	
		for ($i=1; $i<=$rowCount; $i++) {
			$salesOrderId	= $p["salesOrderId_".$i];
			$adjustWt	= $p["adjustWt_".$i];
			$totalWt	= $p["totalWt_".$i];
			$ratePerKg	= $p["ratePerKg_".$i];
			$freightCost	= $p["freightCost_".$i];
			$fovRate	= $p["fovRate_".$i];
			$docketRate	= $p["docketRate_".$i];
			$octroiRate	= $p["octroiRate_".$i];
			$transTotalAmt	= $p["transTotalAmt_".$i];
			$serviceTaxRate = $p["serviceTaxRate_".$i];
			$transGrandTotalAmt = $p["transGrandTotalAmt_".$i];
			$billNo		= $p["billNo_".$i];
			$actualCost	= $p["actualCost_".$i];
			$odaRate	= $p["odaRate_".$i];
			$surcharge	= $p["surcharge_".$i];
				
			$reEdited	= 	$p["reEdit_".$i];
			
			if ($reEdited=="" || $isAdmin==true || $reEdit==true || $companySpecific==true) {
				$settled	= ($p["settled_".$i]=="")?N:$p["settled_".$i];
			} else {
				$settled = "";
			}
			// "<br>======================$salesOrderId & $settled & $actualCost";
			// && $transGrandTotalAmt!=0
			if ($salesOrderId!="" && $settled!="" && $actualCost!="") {
				$updateTransporterPayment = $transporterAccountObj->updateTransporterPayment($salesOrderId, $adjustWt, $totalWt, $ratePerKg, $freightCost, $fovRate, $docketRate, $octroiRate, $transTotalAmt, $serviceTaxRate, $transGrandTotalAmt, $billNo, $settled, $actualCost, $billType, $odaRate, $surcharge);
			}
		}
		if ($updateTransporterPayment!="") {
			$accountSettled = true;
		}		
	}
	
	if ($p["transporter"]=="") $selTransporter = $g["transporter"];
	else $selTransporter = $p["transporter"];

	if ($p["billType"]=="") $billType = $g["billType"];
	else $billType = $p["billType"];
	
	# select record between selected date
	if ($p["supplyFrom"]=="" && $p["supplyTill"]=="") {
		$dateFrom = $g["supplyFrom"];
		$dateTill = $g["supplyTill"];
	} else {
		$dateFrom = $p["supplyFrom"];
		$dateTill = $p["supplyTill"];
	}
		
	$pagingSelection = "supplyFrom=$dateFrom&supplyTill=$dateTill&transporter=$selTransporter&billType=$billType";
	
	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------
	
	#Select the records based on date
	if ($dateFrom!="" && $dateTill!="") {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);	
		# Get all Transporter	
		$transporterRecords	= $transporterAccountObj->fetchTransporterRecords($fromDate, $tillDate);
		if ($billType!="" && ($p["cmdSearch"]!="" || $accountSettled || $pageNo) ) {
			# Get Paging Records
			$transporterInvoiceRecords = $transporterAccountObj->fetchTransporterInvoicePagingRecords($selTransporter, $fromDate, $tillDate, $offset, $limit, $billType);
			# Get All Records
			$getAllTransInvoiceRecords = $transporterAccountObj->filterTransporterInvoiceRecords($selTransporter, $fromDate, $tillDate, $billType);
			$numrows = sizeof($getAllTransInvoiceRecords);

			# Check Bill No Not Required
			//$billNoNotRequired = $transporterAccountObj->chkBillNoRequired();			
		}
	} //Date condition

	## -------------- Pagination Settings II -------------------
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	# Find Grand Totals
	if (sizeof($getAllTransInvoiceRecords)>0 && $billType=='OD') {

		$grandTotalTransporterAmt = 0;
		$grandTotalSettledAmount  = 0;
		$grandTotalDuesAmount	= 0;
		foreach ($getAllTransInvoiceRecords as $tir) {
			$salesOrderId		= $tir[0];
			$salesOrderNo		= $tir[1];
			$distributorId		= $tir[2];
			$soDate			= dateFormat($tir[3]);
			$despatchDate		= dateFormat($tir[4]);
			$stateId		= $tir[5];
			$invoiceValue		= number_format($tir[9],2,'.',''); // Grand Total Invoice Amt
			$cityId			= $tir[10];
			$grossWt		= $tir[14];
			$numBox			= $tir[15];
			$transporterId		= $tir[17];
			$docketNum		= $tir[18];
			$distributorName	= $tir[19];
			$cityName		= $tir[21];
			//Round off Calculation
			$adjWt 	= $transporterAccountObj->getRoundoffVal($grossWt);
			$totalWt		= $grossWt+$adjWt;
			$transporterRateListId  = $tir[22];
			$transOCRateListId	= $tir[28];
			if ($docketNum!="") {
				list($groupedTotalWt, $numGroup) = $transporterAccountObj->getTrptrRecs($offset, $limit, $transporterId, $fromDate, $tillDate,  $billType, $distributorId, $cityId, $docketNum);

				$selGrossWt = ($numGroup>1)?$groupedTotalWt:$totalWt;
				# Find the Transporter rate Per Kg
				list($ratePerKg, $transporterRateEntryId, $rateType) = $transporterAccountObj->getTransporterRate($transporterId, $transporterRateListId, $stateId, $cityId, $selGrossWt);
				
				$freightCost = "";
				$docketCharge = "";
				$FOV = "";
				$total = "";
				$serviceTaxRate = "";
				$grandTotal = "";
				$surchargeAmt = "";
				if ($ratePerKg!="" && $ratePerKg!=0) {
					$freightCost	= $totalWt*$ratePerKg;
					$freightCost 	= ($rateType!="FRC")?$freightCost:$ratePerKg;
					# Get Other Charges
					# FOV $fovCharge=%, $docketCharge=Rs, $serviceTax=%, $octroiServiceCharge = %, Surcharge %
					list($fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $odaCharge, $surchargePercent) = $transporterAccountObj->getTransporterOtherCharges($transporterId, $transOCRateListId);
					$docketCharge 	= $docketCharge/$numGroup;
					$odaCharge 	= $odaCharge/$numGroup;
					$odaApplicable = $tir[45];
					$selOdaRate    = $tir[46];
					$odaRate       = ($odaApplicable=='Y')?(($selOdaRate!=0)?$selOdaRate:$odaCharge):"";

					$FOV	= number_format((($invoiceValue*$fovCharge)/100),2,'.','');	
					$total = $freightCost+$FOV+$docketCharge+$odaRate;
					# Surcharge calc
					$calcSurchargeAmt = $freightCost+($FOV+$docketCharge+$odaRate);
					$surchargeAmt = number_format((($calcSurchargeAmt*$surchargePercent)/100),2,'.','');
					$total += $surchargeAmt;

					$serviceTaxRate = number_format((($total*$serviceTax)/100),2,'.','');		
					$grandTotal = $total+$serviceTaxRate;
					$tActualCost	= ($tir[31]!="" && $tir[31]!=0)?$tir[31]:$grandTotal ;
				
					$billNo	    	= $tir[23];
					$settldStatus	= $tir[24];
		
					$settledDate 	= $tir[25];
					
					if ($settldStatus=='Y') {
						//$checked	=	"Checked";
						$grandTotalSettledAmount += $tActualCost;
					} else {
						//$checked	=	"";
						$grandTotalDuesAmount	+= $tActualCost;
					}
					$disabled = "";
					$edited	  = "";
					if ($settldStatus=='Y' && $isAdmin==false && $reEdit==false) {
						$disabled = "readonly";
						$edited	  = 1;
					}
					$grandTotalTransporterAmt += $tActualCost;
				} // Rate per kg ends here
			} // Docket No ends here
		}
	}

	if (sizeof($getAllTransInvoiceRecords)>0 && $billType=='OC') {

		$grandTotalTransporterAmt = 0;
		$grandTotalSettledAmount  = 0;
		$grandTotalDuesAmount	= 0;
		foreach ($getAllTransInvoiceRecords as $tir) {
			$salesOrderId		= $tir[0];
			$salesOrderNo		= $tir[1];
			$distributorId		= $tir[2];
			$soDate			= dateFormat($tir[3]);
			$despatchDate		= dateFormat($tir[4]);
			$stateId		= $tir[5];
			$invoiceValue		= number_format($tir[9],2,'.','');	// Grand Total Invoice Amt
			$cityId			= $tir[10];
			$grossWt		= $tir[14];
			$numBox			= $tir[15];
			$transporterId		= $tir[17];
			$docketNum		= $tir[18];
			$distributorName	= $tir[19];
			$cityName		= $tir[21];
			//Round off Calculation
			$adjWt 	= $transporterAccountObj->getRoundoffVal($grossWt);
			$totalWt		= $grossWt+$adjWt;
			$transporterRateListId  = $tir[22];
			$transOCRateListId	= $tir[28];

			if ($docketNum!="") {
				# Get Other Charges
				# FOV $fovCharge=%, $docketCharge=Rs, $serviceTax=%, $octroiServiceCharge = %
				list($fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $odaCharge) = $transporterAccountObj->getTransporterOtherCharges($transporterId, $transOCRateListId);	
	
				$settledDate 	= $tir[25];
				$octroiExempted = $tir[41];
				$octroiPercent = ($octroiExempted!='Y')?$cityMasterObj->getOctroiPercent($cityId):0;
	
				$octroiValue    = number_format((($invoiceValue*$octroiPercent)/100),2,'.','');
				$serviceTaxRate = number_format((($octroiValue*$octroiServiceCharge)/100),2,'.','');	
				$grandTotal	= $octroiValue+$serviceTaxRate;
	
				$billNo	    	= $tir[38];
				$settldStatus	= $tir[39];
				$settledDate 	= $tir[40];
				$tActualCost	= ($tir[37]!="" && $tir[37]!=0)?$tir[37]:$grandTotal ;
				if ($settldStatus=='Y') {
					//$checked	=	"Checked";
					$grandTotalSettledAmount += $tActualCost;
				} else {
					//$checked	=	"";
					$grandTotalDuesAmount	+= $tActualCost;
				}
				$disabled = "";
				$edited	  = "";
				if ($settldStatus=='Y' && $isAdmin==false && $reEdit==false) {
					$rDisabled = "readonly";
					$rEdited	  = 1;
				}
				$grandTotalTransporterAmt += $tActualCost;
			} // Docket No ends here
		}
	}
	

	# Display heading
	if ($editMode)	$heading	= $label_editTransporterACSettlement;
	else		$heading	= $label_addTransporterACSettlement;	

	
	$ON_LOAD_PRINT_JS	= "libjs/TransporterAccount.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmTransporterAccount" action="TransporterAccount.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<tr>
			<td height="30" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>		
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="60%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2"  align="center">
										<table cellpadding="0"  width="99%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td colspan="2" height="5"></td>
                      </tr>
			<? if (sizeof($transporterInvoiceRecords)>0) {?>
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center">
				<? if($isAdmin==true || $reEdit==true || $companySpecific==true || $edit==true){?>
					<input name="cmdSaveTransporterAccount" type="submit" class="button" id="cmdSaveTransporterAccount" onClick="return updateTransporterAccount(document.frmTransporterAccount);" value=" Save ">
				<? }?>
				&nbsp;&nbsp;
<? if($print==true){?>	<input type="button" name="Submit" value="Print" class="button" onClick="return printWindow('PrintTransporterAccount.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&transporter=<?=$selTransporter?>&offset=<?=$offset?>&limit=<?=$limit?>&pageNo=<?=$pageNo?>&billType=<?=$billType?>',700,600);"><? }?>
			</td>
                        <?} ?>
                      </tr>
		<?php
			}
		?>
                      <input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
		
                      <tr>
                        <td colspan="3" nowrap height="25"></td>
                        </tr>
                      <tr>
                        <td colspan="2" align="center" style="padding-left:10px;padding-right:10px;">
			<table width="250">
                                  <tr> 
                                    <td class="fieldName">*From:</td>
                                    <td> 
                                      <input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" autocomplete="off" onchange="submitForm('supplyFrom','supplyTill',document.frmTransporterAccount);">
				</td>
				<td class="fieldName">*To:</td>
				<td>
                                        <input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" autocomplete="off" onChange="submitForm('supplyFrom','supplyTill',document.frmTransporterAccount);">
					</td>
					 <td class="fieldName" nowrap="true">Transporter:</td>
                                    <td>
                                      <select name="transporter" id="transporter">
                                        <option value="">-- Select All--</option>
                                        <?
					foreach ($transporterRecords as $tr) {
						$transporterId	=	$tr[0];
						$transporterName	=	stripSlash($tr[1]);
						$selected	=	"";
						if ($transporterId == $selTransporter) {
							$selected	=	"selected";
						}
					?>
                                        <option value="<?=$transporterId?>" <?=$selected?>> 
                                        <?=$transporterName?>
                                        </option>
                                        <? } ?>
                                      </select>
				</td>
	<td nowrap="true">
		<table cellpadding="0" cellspacing="0">
		<TR>
			<TD class="fieldName" nowrap="true" style="padding-left:5px;padding-right:5px;">
				*Bill Type:
			</TD>
			<td>
				<select name="billType" id="billType">
					<option value="">-- Select --</option>
					<option value="OD" <? if ($billType=='OD') echo "selected"; ?>>Ordinary Bill</option>
					<option value="OC" <? if ($billType=='OC') echo "selected"; ?>>Octroi Bill</option>
				</select>
			</td>
		</TR>
		</table>
	</td>
	<td>
		<input name="cmdSearch" type="submit" id="cmdSearch" value=" Search" class="button" onclick="return validateTransporterAccount(document.frmTransporterAccount);">
	</td>
                                  </tr>
                                </table>
			</td>
                        </tr>	  
	<tr><TD height="10"></TD></tr>
	<?php
	     if (sizeof($transporterInvoiceRecords)>0 && $billType=='OD') {
		  $i = 0;
			if (!$selTransporterName) $colSpan = 24;
			else $colSpan	= 23;
			//if ($billNoNotRequired) $colSpan+=1 ;
			// default col span = 20
			$odRowHStyle = "padding-left:2px; padding-right:2px;font-size:11px; line-height:normal;";
	?>
        <tr>
              <td colspan="4" align="center" style="padding-left:10px; padding-right:10px;">
		<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" align="center">
		<tr bgcolor="White">
	     	<td colspan="<?=$colSpan?>" align="right" style="padding-right:10px;" height="30">
			<?php if ($assignDktNoAccess) {?>
				<a href="TransporterSpex.php?url=TransporterRateMaster.php?transporterFilter=<?=$selTransporter?>" class="link1" title="Click to manage Transporter Rate.">Transporter Rate Master</a>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<?php }?>
			<a href="###" class="link1" title="Click to manage Transporter Docket No." onClick="return printWindow('AssignDocketNo.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&transporter=<?=$selTransporter?>&popupWindow=1',700,600);">Assign Docket No</a>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="TransporterSettlementSummary.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&transporter=<?=$selTransporter?>" class="link1" title="Click to Pay the Bills">View Settlement Summary</a>			
		</td>
		</tr>
		<? if($maxpage>1){?>
	   <tr bgcolor="White">
	     <td colspan="<?=$colSpan?>" align="right" style="padding-right:10px;"><div align="right">
	  <?php 				 			  
		$nav  = '';
		for($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
			} else {
			      	$nav.= " <a href=\"TransporterAccount.php?$pagingSelection&pageNo=$page\" class=\"link1\">$page</a> ";
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterAccount.php?$pagingSelection&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterAccount.php?$pagingSelection&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>
	  </div></td></tr><? }?>
              	<tr bgcolor="#f2f2f2" align="center"> 
                	<th nowrap="nowrap" class="listing-head" style="<?=$odRowHStyle?>">Date</th>
               		<th align="center" class="listing-head" style="<?=$odRowHStyle?>">Invoice No</th>
                	<th class="listing-head" style="<?=$odRowHStyle?>">Inv Value</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Distributor</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">City</th>
			<?php
				if (!$selTransporter) {
			?>
			<th class="listing-head" style="<?=$odRowHStyle?>">Transporter</th>
			<? }?>
			<th class="listing-head" style="<?=$odRowHStyle?>">Docket No.</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">No of Boxes</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Gross Wt</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Adj</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Total Wt</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Rate/Kg</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Freight Cost</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">FOV</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Docu Charges</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">ODA Charges</th>	
			<th class="listing-head" style="<?=$odRowHStyle?>">Surcharge</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Total</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Serv Tax</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Grand Total</th>
			<?php
				//if ($billNoNotRequired) {
			?>
			<th class="listing-head" style="<?=$odRowHStyle?>">Actual Cost</th>
			<? //}?>
			<th class="listing-head" style="<?=$odRowHStyle?>">Bill No</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Setld</th>
			<th class="listing-head" style="<?=$odRowHStyle?>">Setl<br/> Date</th>
              </tr>
              <?php
		$totalTransporterAmt = 0;
		$totalActualCost = 0;
		$actualCost = 0;
		foreach($transporterInvoiceRecords as $tir){
			$i++;
			$salesOrderId		= $tir[0];
						
			$soNo 	= $tir[1];
			$invType = $tir[32];			
			$pfNo 	= $tir[33];
			$saNo	= $tir[34];
			$salesOrderNo = "";
			if ($soNo!=0) $salesOrderNo=$soNo;
			else if ($invType=='T') $salesOrderNo = "P$pfNo";
			else if ($invType=='S') $salesOrderNo = "S$saNo";
			
			$distributorId		= $tir[2];
			$soDate			= dateFormat($tir[3]);
			$despatchDate		= dateFormat($tir[4]);
			$stateId		= $tir[5];
			$invoiceValue		= number_format($tir[9],2,'.','');	// Grand Total Invoice Amt
			$cityId			= $tir[10];
			$grossWt		= $tir[14];
			$numBox			= $tir[15];
			$transporterId		= $tir[17];
			$docketNum		= $tir[18];
			$distributorName	= $tir[19];
			$cityName		= $tir[21];
			//Round off Calculation
			$adjWt 	= $transporterAccountObj->getRoundoffVal($grossWt);
			$totalWt		= $grossWt+$adjWt;
			$transporterRateListId  = $tir[22];
			$transOCRateListId	= $tir[28];
			
			// Trans Id, Function Type, Despatch ate
			if ($transOCRateListId=="") {
				//$transOCRateListId = $transporterRateListObj->latestRateList($transporterId, "TOC");
				//$getId = $transporterRateListObj->getTransporterValidRateListId($transporterId, "TOC", $tir[4]);
			}
			$actualCost = 0;
			if ($docketNum!="") {
				list($groupedTotalWt, $numGroup) = $transporterAccountObj->getTrptrRecs($offset, $limit, $transporterId, $fromDate, $tillDate,  $billType, $distributorId, $cityId, $docketNum);

				$selGrossWt = ($numGroup>1)?$groupedTotalWt:$totalWt;
				//echo "$selGrossWt====$docketNum<br>";

				# Find the Transporter rate Per Kg
				list($ratePerKg, $transporterRateEntryId, $rateType) = $transporterAccountObj->getTransporterRate($transporterId, $transporterRateListId, $stateId, $cityId, $selGrossWt);

				//$ratePerKg = "";
				$freightCost = "";
				$FOV	     = "";	
				$total	     = "";
				$serviceTaxRate = "";
				$grandTotal	= "";
				$docketCharge	= "";	
				$surchargeAmt = "";			
				if ($ratePerKg!="" && $ratePerKg!=0) {
					$freightCost	= $totalWt*$ratePerKg;	
					$freightCost 	= ($rateType!="FRC")?$freightCost:$ratePerKg;
					# Get Other Charges
					# FOV $fovCharge=%, $docketCharge=Rs, $serviceTax=%, $octroiServiceCharge = %
					list($fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $odaCharge, $surchargePercent) = $transporterAccountObj->getTransporterOtherCharges($transporterId, $transOCRateListId);
					$docketCharge 	= number_format(($docketCharge/$numGroup),2,'.','');
					$odaCharge 	= number_format(($odaCharge/$numGroup),2,'.','');					

					$odaApplicable = $tir[45];
					$selOdaRate    = $tir[46];
					$odaRate       = ($odaApplicable=='Y')?(($selOdaRate!=0)?$selOdaRate:$odaCharge):"";
					$FOV	= number_format((($invoiceValue*$fovCharge)/100),2,'.','');
					$total = $freightCost+$FOV+$docketCharge+$odaRate;

					# Surcharge calc
					$calcSurchargeAmt = $freightCost+($FOV+$docketCharge+$odaRate);
					$surchargeAmt = number_format((($calcSurchargeAmt*$surchargePercent)/100),2,'.','');
					$total += $surchargeAmt;
				
					$serviceTaxRate = number_format((($total*$serviceTax)/100),2,'.','');
					$grandTotal = $total+$serviceTaxRate;
					$totalTransporterAmt += $grandTotal;
					$actualCost	= ($tir[31]!="" && $tir[31]!=0)?$tir[31]:$grandTotal ;
					$totalActualCost +=	$actualCost;	
				}
			} // Docket No check
			$billNo	    	= $tir[23];
			$settldStatus	= $tir[24];
			$settledDate 	= $tir[25];
			
			if ($settldStatus=='Y') {
				$checked	=	"Checked";				
			} else {
				$checked	=	"";
			}			
			$disabled = "";
			$edited	  = "";
			if ($settldStatus=='Y' && $isAdmin==false && $reEdit==false) {
				$disabled = "readonly";
				$edited	  = 1;
			}
			

			$selTransporterName = $tir[29];
			$billRequired    = $tir[30];
			
			$txtStyleDisplay = "";
			$readOnly = "";
			if ($billRequired=='N') {
				$txtReadOnly = " readonly='true' ";
				$txtStyleDisplay = " style='border:none;'";	
			}	

			$rowColor = "";
			$disErrMsg = "";
			if (($ratePerKg=="" || $ratePerKg==0) || $docketNum=="" )  {
				$rowColor = "#FFFFCC";
				//$disErrMsg = "onMouseover=\"ShowTip('Please define Transporter rate per Kg (Zone Wise).');\" onMouseout=\"UnTip();\"";
			} else $rowColor = "#FFFFFF";
			
			$reSetldODDate = $tir[42];
			if ($reSetldODDate!="") $rowColor = "#FFFFCC";

			$deliveryDate	= ($tir[44]!='0000-00-00')?dateFormat($tir[44]):"";
			if ($docketNum=="" || ($ratePerKg=="" || $ratePerKg==0)) $disabled = "readonly";
			//echo "<b>$actualCost";
		?>
              <tr bgcolor="<?=$rowColor?>" <?=$disErrMsg?>> 
                <td class="listing-item" nowrap height='25' style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$despatchDate?>
			<input type="hidden" name="salesOrderId_<?=$i?>" value="<?=$salesOrderId?>">
			<input type="hidden" name="billRequired_<?=$i?>" id="billRequired_<?=$i?>" value="<?=$billRequired?>">
		</td>
                <td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$salesOrderNo?>
		</td>
                <td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$invoiceValue?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px; font-size:11px; line-height:normal;">
			<?=$distributorName?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$cityName?>
		</td>
		<?php
				if (!$selTransporter) {
		?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px; font-size:11px; line-height:normal;">
			<?=$selTransporterName?>
		</td>
		<? }?>
		<td class="listing-item" align="center" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!="")?$docketNum:"<span class='err1'><b>NA</b></span>";?>
			<input type="hidden" name="docketNum_<?=$i;?>" id="docketNum_<?=$i;?>" value="<?=$docketNum?>">
			<input type="hidden" name="deliveryDate_<?=$i;?>" id="deliveryDate_<?=$i;?>" value="<?=$deliveryDate?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!="")?$numBox:"&nbsp;"?><?//$numBox?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!="")?$grossWt:"&nbsp;"?><?//$grossWt?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!="")?number_format($adjWt,2,'.',''):"&nbsp;"?>
			<?//number_format($adjWt,2,'.','')?>
			<input type="hidden" name="adjustWt_<?=$i;?>" value="<?=$adjWt?>">	
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//$totalWt?>
			<?=($docketNum!="")?$totalWt:"&nbsp;"?>
			<input type="hidden" name="totalWt_<?=$i;?>" value="<?=$totalWt?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//$ratePerKg?>	
			<?=($docketNum!="")?(($ratePerKg!=0)?$ratePerKg:"<span class='err1'><b>NA</b></span>"):"&nbsp;"?>
			<input type="hidden" name="ratePerKg_<?=$i;?>" value="<?=$ratePerKg?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//number_format($freightCost,2,'.','')?>
			<?=($docketNum!="" && $freightCost!=0)?number_format($freightCost,2,'.',''):"&nbsp;"?>
			<input type="hidden" name="freightCost_<?=$i;?>" id="freightCost_<?=$i;?>" value="<?=$freightCost?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//$FOV?>
			<?=($docketNum!="")?$FOV:"&nbsp;"?>	
			<input type="hidden" name="fovRate_<?=$i;?>" id="fovRate_<?=$i;?>" value="<?=$FOV?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">			
			<?=($docketNum!="")?$docketCharge:"&nbsp;"?>
			<input type="hidden" name="docketRate_<?=$i;?>" id="docketRate_<?=$i;?>" value="<?=$docketCharge?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!="")?$odaRate:""?>
			<input type="hidden" name="odaRate_<?=$i?>" id="odaRate_<?=$i?>" value="<?=$odaRate?>" size="5" style="text-align:right;" autocomplete="off">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!="" && $surchargeAmt!=0)?$surchargeAmt:"&nbsp;"?>
			<input type="hidden" name="surcharge_<?=$i?>" id="surcharge_<?=$i?>" value="<?=$surchargeAmt?>" size="5" style="text-align:right;" autocomplete="off" readonly>
		</td>	
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" id="trptrTotalAmtCol_<?=$i;?>">
			<?//number_format($total,2,'.','')?>
			<?=($docketNum!="" && $total!=0)?number_format($total,2,'.',''):"&nbsp;"?>
			<input type="hidden" name="transTotalAmt_<?=$i;?>" id="transTotalAmt_<?=$i;?>" value="<?=$total?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//$serviceTaxRate?>
			<?=($docketNum!="")?$serviceTaxRate:"&nbsp;"?>
			<input type="hidden" name="serviceTaxRate_<?=$i;?>" value="<?=$serviceTaxRate?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//number_format($grandTotal,2,'.','')?>
			<?=($docketNum!="")?$grandTotal:"&nbsp;"?>
			<input type="hidden" name="transGrandTotalAmt_<?=$i;?>" value="<?=$grandTotal?>">
		</td>
		<?php
			//if ($billRequired=='N') {
		?>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<input type="text" name="actualCost_<?=$i?>" id="actualCost_<?=$i?>" size="8" value="<?=($actualCost!=0)?number_format($actualCost,2,'.',''):"";?>" autocomplete="off" style="text-align:right;" onkeyup="calcActualCost();" onkeydown="return nextBox(event,'document.frmTransporterAccount','actualCost_<?=$i+1;?>');" <?=$disabled?>>
		</td>
		<?php
			//} else if ($billNoNotRequired) {
		?>
		<!--<td>&nbsp;</td>-->
		<? //}?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<input type="text" name="billNo_<?=$i?>" id="billNo_<?=$i?>" size="6" value="<?=$billNo?>" autocomplete="off" onkeydown="return nextBox(event,'document.frmTransporterAccount','billNo_<?=$i+1;?>');" <?=$txtStyleDisplay?> <?=$txtReadOnly?>>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<input name="settled_<?=$i;?>" type="checkbox" id="settled_<?=$i;?>" value="Y"  class="chkBox" <?=$checked?> <?=$disabled?>>
			<input type="hidden" name="reEdit_<?=$i;?>" value="<?=$edited?>">
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;line-height:normal;">
			<?=($settledDate!="0000-00-00" && $settledDate!="")?dateFormat($settledDate):""?>
			<?php if ($reSetldODDate!="") {?>
				<br/>
				<span class="listing-item" style="line-height:normal;font-size:9px;color:maroon">Resetld On:<?=dateFormat($reSetldODDate);?></span>
			<? }?>
		</td>	
              </tr>
		<?php
			}
		?>
	<tr bgcolor="#FFFFFF">
		<?php
			if (!$selTransporter) $totRowColSpan = 19;
			else $totRowColSpan = 18;
		?>
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" colspan="<?=$totRowColSpan?>" align="right">Total:</td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;" align="right"><strong><?=number_format($totalTransporterAmt,2,'.','')?></strong></td>
		<?php
			//if ($billNoNotRequired) {
		?>
		<td  align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<input type="text" name="totalActualCost" id="totalActualCost" size="10" value="<?=number_format($totalActualCost,2,'.','');?>" autocomplete="off" style="text-align:right; border:none; font-weight:bold;" readonly>
		</td>
		<?php
			//}
		?>
		<td>&nbsp;</td>
		<td>&nbsp;</td>		
		<td>&nbsp;</td>
	</tr>	
		 <? if($maxpage>1){?>
	  <tr bgcolor="#FFFFFF">
		<td colspan="<?=$colSpan?>" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
			} else {
      				$nav.= " <a href=\"TransporterAccount.php?$pagingSelection&pageNo=$page\" class=\"link1\">$page</a> ";
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterAccount.php?$pagingSelection&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterAccount.php?$pagingSelection&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>
	  </div><input type="hidden" name="pageNo" value="<?=$pageNo?>"></td></tr>
	  <? }?>
	<tr bgcolor="#FFFFFF">
<td colspan="<?=$colSpan?>" height="25">
  <table cellpadding="0" cellspacing="0">
  <tr>
    <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Total Value:</td>
    <td class="listing-item" align="left">
	<strong><?=number_format($grandTotalTransporterAmt,2,'.',',')?></strong>
   </td>
    <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;"> Settled:</td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalSettledAmount,2,'.',',')?></strong>
  </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Pending: </td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalDuesAmount,2,'.',',');?></strong>
   </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Paid:</td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalSettledAmount,2,'.',',');?></strong>
 </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Due:</td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalDuesAmount,2,'.',',');?></strong>
  </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;"> Payable</td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalDuesAmount,2,'.',',');?></strong>
  </td>
  </tr>
  </table>
  </td></tr>
      </table>
	</td>
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
       </tr>
	<?php
		}
	?>
	<tr><TD height="5"></TD></tr>
	<?php
		if (sizeof($transporterInvoiceRecords)>0 && $billType=='OC') {
			if (!$selTransporterName) $OBColSpan = 19;
			else $OBColSpan	= 18;
			//if ($billNoNotRequired) $OBColSpan+=1 ;
			$octroiRowHeadStyle = "padding-left:2px; padding-right:2px; font-size:11px; line-height:normal;";
	?>
	<tr>
		<TD colspan="5" style="padding-left:10px; padding-right:10px;">
			<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" align="center">	
		<!--<tr bgcolor="white">
			<TD class="listing-head" colspan="18" nowrap="true">Octroi Settlement</TD>
		</tr>-->
		<tr bgcolor="White">
	     	<td colspan="<?=$OBColSpan?>" align="right" style="padding-right:10px;" height="30">
			<a href="###" class="link1" title="Click to manage Transporter Docket No." onClick="return printWindow('AssignDocketNo.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&transporter=<?=$selTransporter?>&popupWindow=1',700,600);">Assign Docket No</a>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="TransporterSettlementSummary.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&transporter=<?=$selTransporter?>" class="link1" title="Click to Pay the Bills">View Settlement Summary</a>
		</td>
		</tr>
		<? if($maxpage>1){?>
	   <tr bgcolor="White">
	     <td colspan="<?=$OBColSpan?>" align="right" style="padding-right:10px;"><div align="right">
	  <?php 				 			  
		$nav  = '';
		for($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
			} else {
			      	$nav.= " <a href=\"TransporterAccount.php?$pagingSelection&pageNo=$page\" class=\"link1\">$page</a> ";
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterAccount.php?$pagingSelection&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterAccount.php?$pagingSelection&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>
	  </div></td></tr><? }?>
              	<tr bgcolor="#f2f2f2" align="center"> 
                	<th nowrap="nowrap" class="listing-head" style="<?=$octroiRowHeadStyle?>">Date</th>
               		<th align="center" class="listing-head" style="<?=$octroiRowHeadStyle?>">Invoice No</th>
                	<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Inv Value</th>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Distributor</th>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">City</th>
			<?php
				if (!$selTransporter) {
			?>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Transporter</th>
			<? }?>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Docket No.</th>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">No of Boxes</th>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Gross Wt</th>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Adj</th>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Total Wt</th>
			<!--<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Rate/Kg</th>-->
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Octroi %</th>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Octroi Value</th>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Serv Tax</th>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Grand Total</th>
			<?php
				//if ($billNoNotRequired) {
			?>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Actual Cost</th>
			<? //}?>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Bill No</th>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Setld</th>
			<th class="listing-head" style="<?=$octroiRowHeadStyle?>">Setl<br/> Date</th>
              </tr>
              <?php
		$totalTransporterAmt = 0;
		$i =0;
		
		//printr($transporterInvoiceRecords);
		foreach($transporterInvoiceRecords as $tir){
			$i++;
			$salesOrderId		= $tir[0];
			$soNo 	= $tir[1];
			$invType = $tir[32];			
			$pfNo 	= $tir[33];
			$saNo	= $tir[34];
			$salesOrderNo = "";
			if ($soNo!=0) $salesOrderNo=$soNo;
			else if ($invType=='T') $salesOrderNo = "P$pfNo";
			else if ($invType=='S') $salesOrderNo = "S$saNo";
			$distributorId		= $tir[2];
			$soDate			= dateFormat($tir[3]);
			$despatchDate		= dateFormat($tir[4]);
			$stateId		= $tir[5];
			$invoiceValue		= number_format($tir[9],2,'.',''); // Grand Total Invoice Amt
			$cityId			= $tir[10];
			$grossWt		= $tir[14];
			$numBox			= $tir[15];
			$transporterId		= $tir[17];
			$docketNum		= $tir[18];
			$distributorName	= $tir[19];
			$cityName		= $tir[21];
			//Round off Calculation
			$adjWt 	= $transporterAccountObj->getRoundoffVal($grossWt);
			$totalWt		= $grossWt+$adjWt;
			$transporterRateListId  = $tir[22];
			$transOCRateListId	= $tir[28];
			
			# Find the Transporter rate Per Kg
			//list($ratePerKg, $transporterRateEntryId, $rateType)		= $transporterAccountObj->getTransporterRate($transporterId, $transporterRateListId, $stateId, $cityId, $totalWt);
			$settldStatus = "";
			$settledDate = "";
			$billNo = "";
			$octroiExempted = "";
			$octroiPercent = "";
			$octroiValue = "";
			$serviceTaxRate = "";
			$grandTotal = "";
			$actualCost = "";
			if ($docketNum!="") {
				# Get Other Charges
				# FOV $fovCharge=%, $docketCharge=Rs, $serviceTax=%, $octroiServiceCharge = %
				list($fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $odaCharge, $surchargePercent) = $transporterAccountObj->getTransporterOtherCharges($transporterId, $transOCRateListId);
	
				$billNo	    	= $tir[38];
				$settldStatus	= $tir[39];
				$settledDate 	= $tir[40];
				$octroiExempted = $tir[41];
				$octroiPercent = ($octroiExempted!='Y')?$cityMasterObj->getOctroiPercent($cityId):0;
	
				$octroiValue    = number_format((($invoiceValue*$octroiPercent)/100),2,'.','');
				$serviceTaxRate = number_format((($octroiValue*$octroiServiceCharge)/100),2,'.','');
				
				$grandTotal	= $octroiValue+$serviceTaxRate;
				$totalTransporterAmt += $grandTotal;
				$actualCost	= ($tir[37]!="" && $tir[37]!=0)?$tir[37]:$grandTotal ;
				$totalActualCost += $actualCost;
			} // Docket No checking ends here
			
			if ($settldStatus=='Y') $checked = "Checked";
			else $checked	=	"";
			
			$disabled = "";
			$edited	  = "";
			if ($settldStatus=='Y' && $isAdmin==false && $reEdit==false) {
				$disabled = "readonly";
				$edited	  = 1;
			}
			
			$rowColor = "";
			$disErrMsg = "";
			if (($octroiPercent=="" || $octroiPercent==0) && $octroiExempted!='Y')  {
				$rowColor = "#FFFFCC";
				$disErrMsg = "onMouseover=\"ShowTip('Please define a Octroi Percent.');\" onMouseout=\"UnTip();\"";
			} else $rowColor = "#FFFFFF";
			//bgcolor="#FFFFFF"
			$selTransporterName = $tir[29];			
			$billRequired    = $tir[30];
			
			$txtStyleDisplay = "";
			$readOnly = "";
			if ($billRequired=='N') {
				$txtReadOnly = " readonly='true' ";
				$txtStyleDisplay = " style='border:none;'";	
			}

			$reSetldOCDate = $tir[43];
			if ($reSetldOCDate!="") $rowColor = "#FFFFCC";
			$deliveryDate	= ($tir[44]!='0000-00-00')?dateFormat($tir[44]):"";

			if ($docketNum=="") {
				$txtReadOnly = " readonly='true' ";
				$disabled = "readonly";
			}			
		?>
              <tr bgcolor="<?=$rowColor?>" <?=$disErrMsg?>> 
                <td class="listing-item" nowrap height='25' style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$despatchDate?>
			<input type="hidden" name="salesOrderId_<?=$i?>" value="<?=$salesOrderId?>">
			<input type="hidden" name="billRequired_<?=$i?>" id="billRequired_<?=$i?>" value="<?=$billRequired?>">
		</td>
                <td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$salesOrderNo?>
		</td>
                <td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$invoiceValue?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px; font-size:11px; line-height: normal;">
			<?=$distributorName?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=$cityName?>
		</td>
		<?php
				if (!$selTransporter) {
		?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px; font-size:11px; line-height:normal;">
			<?=$selTransporterName?>
		</td>
		<? }?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?=($docketNum!="")?$docketNum:"<span class='err1'><b>NA</b></span>";?>
			<input type="hidden" name="docketNum_<?=$i;?>" id="docketNum_<?=$i;?>" value="<?=$docketNum?>">
			<input type="hidden" name="deliveryDate_<?=$i;?>" id="deliveryDate_<?=$i;?>" value="<?=$deliveryDate?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//$numBox?>
			<?=($docketNum!="")?$numBox:"&nbsp;"?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//$grossWt?>
			<?=($docketNum!="")?$grossWt:"&nbsp;"?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//number_format($adjWt,2,'.','')?>
			<?=($docketNum!="")?number_format($adjWt,2,'.',''):"&nbsp;"?>
			<input type="hidden" name="adjustWt_<?=$i;?>" value="<?=$adjWt?>">	
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//$totalWt?>
			<?=($docketNum!="")?$totalWt:"&nbsp;"?>
			<input type="hidden" name="totalWt_<?=$i;?>" value="<?=$totalWt?>">
			<input type="hidden" name="ratePerKg_<?=$i;?>" value="<?=$ratePerKg?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//$octroiPercent?>
			<?=($docketNum!="")?$octroiPercent:"&nbsp;"?>
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//number_format($octroiValue,2,'.','');?>
			<?=($docketNum!="")?number_format($octroiValue,2,'.',''):"&nbsp;"?>
			<input type="hidden" name="octroiRate_<?=$i;?>" value="<?=$octroiValue?>">
		</td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//number_format($serviceTaxRate,2,'.','');?>
			<?=($docketNum!="")?number_format($serviceTaxRate,2,'.',''):"&nbsp;"?>
			<input type="hidden" name="serviceTaxRate_<?=$i;?>" value="<?=$serviceTaxRate?>">
		</td>		
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<?//$grandTotal?>
			<?=($docketNum!="")?$grandTotal:"&nbsp;"?>	
			<input type="hidden" name="transGrandTotalAmt_<?=$i;?>" value="<?=$grandTotal?>">
		</td>
		<?php
			//if ($billRequired=='N') {
		?>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<input type="text" name="actualCost_<?=$i?>" id="actualCost_<?=$i?>" size="8" value="<?=($actualCost!=0)?$actualCost:"";?>" autocomplete="off" style="text-align:right;" onkeyup="calcActualCost();" <?=$disabled?>>
		</td>
		<?php
			//} else if ($billNoNotRequired) {
		?>
		<!--<td>&nbsp;</td>-->
		<?php
			// }
		?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<input type="text" name="billNo_<?=$i?>" id="billNo_<?=$i?>" size="6" value="<?=$billNo?>" autocomplete="off" onkeydown="return nextBox(event,'document.frmTransporterAccount','billNo_<?=$i+1;?>');" <?=$txtStyleDisplay?> <?=$txtReadOnly?> />
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;">
			<input name="settled_<?=$i;?>" type="checkbox" id="settled_<?=$i;?>" value="Y"  class="chkBox" <?=$checked?> <?=$disabled?>>
			<input type="hidden" name="reEdit_<?=$i;?>" value="<?=$edited?>">
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;line-height:normal;">
			<?=($settledDate!="0000-00-00" && $settledDate!="")?dateFormat($settledDate):""?>
			<?php if ($reSetldOCDate!="") {?>
			<br/>
			<span class="listing-item" style="line-height:normal;font-size:9px;color:maroon">Resetld On:<?=dateFormat($reSetldOCDate);?></span>
			<? }?>
		</td>	
              </tr>
		<?php
			}
		?>
	<tr bgcolor="#FFFFFF">
		<?php
			if (!$selTransporter) $totOBRowColSpan = 14;
			else $totOBRowColSpan = 13;
		?>
		<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
			<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" colspan="<?=$totOBRowColSpan?>" align="right">Total:</td>
			<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;" align="right"><strong><?=$totalTransporterAmt?></strong></td>
			<?php
			//if ($billNoNotRequired) {
			?>
			<td  align="right" style="padding-left:2px; padding-right:2px;font-size:11px;">
				<input type="text" name="totalActualCost" id="totalActualCost" size="10" value="<?=number_format($totalActualCost,2,'.','');?>" autocomplete="off" style="text-align:right; border:none; font-weight:bold;" readonly>
			</td>
			<?php
				//}
			?>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
	</tr>	
	<? if($maxpage>1){?>
	  <tr bgcolor="#FFFFFF">
		<td colspan="<?=$OBColSpan?>" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
			} else {
      				$nav.= " <a href=\"TransporterAccount.php?$pagingSelection&pageNo=$page\" class=\"link1\">$page</a> ";
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterAccount.php?$pagingSelection&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterAccount.php?$pagingSelection&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>
	  </div><input type="hidden" name="pageNo" value="<?=$pageNo?>"></td></tr>
	  <? }?>
	<tr bgcolor="#FFFFFF">
<td colspan="<?=$OBColSpan?>" height="25">
  <table cellpadding="0" cellspacing="0">
  <tr>
    <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Total Value:</td>
    <td class="listing-item" align="left">
	<strong><?=number_format($grandTotalTransporterAmt,2,'.',',')?></strong>
   </td>
    <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;"> Settled:</td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalSettledAmount,2,'.',',')?></strong>
  </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Pending: </td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalDuesAmount,2,'.',',');?></strong>
   </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Paid:</td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalSettledAmount,2,'.',',');?></strong>
 </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;">Due:</td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalDuesAmount,2,'.',',');?></strong>
  </td>
  <td class="listing-head" style="padding-left:10px; padding-right:5px;font-size:11px;"> Payable</td>
  <td class="listing-item">
	<strong><?=number_format($grandTotalDuesAmount,2,'.',',');?></strong>
  </td>
  </tr>
  </table>
  </td></tr>	
      </table>
		</TD>
	</tr>
	<?php
		} // Octroi rec size ceck ends herre
	?>
	<?php 
		//} 
	?>
	<tr><TD height="5"></TD></tr>	
                      <tr> 
                        <td colspan="4" align="center" class="err1"><? if(sizeof($transporterInvoiceRecords)<=0 && $selTransporter!=""){ echo $msgNoSettlement;}?></td>
                        </tr>
			<tr>
                        <td colspan="3" nowrap height="5"></td>
                        </tr>
	<? if (sizeof($transporterInvoiceRecords)>0) {?>
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center">
				<? if($isAdmin==true || $reEdit==true || $companySpecific==true || $edit==true){?>
					<input name="cmdSaveTransporterAccount" type="submit" class="button" id="cmdSaveTransporterAccount" onClick="return updateTransporterAccount(document.frmTransporterAccount);" value=" Save ">
				<? }?>
				&nbsp;&nbsp;
<? if($print==true){?>	<input type="button" name="Submit" value="Print" class="button" onClick="return printWindow('PrintTransporterAccount.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&transporter=<?=$selTransporter?>&offset=<?=$offset?>&limit=<?=$limit?>&pageNo=<?=$pageNo?>&billType=<?=$billType?>',700,600);"><? }?>
			</td>
                        <input type="hidden" name="cmdAddNew" value="1">
                        <?}?>
                      </tr>
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
				<!-- Form fields end   -->
			</td>
		</tr>	
		<tr>
			<td height="10" ></td>
		</tr>
	
			
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
	
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
