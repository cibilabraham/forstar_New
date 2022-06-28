<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	true;
	$selChallanNo	=	"";
	$dateFrom		=	"";
	$dateTill		=	"";
	$settledAmount	=	"";
	$duesAmount		=	"";
	$updated		=	false;
	
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
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	//----------------------------------------------------------

	#Find the Current Rate List
	//$currentRateList = $processratelistObj->latestRateList();

	if ($p["cmdAddProcessorsAccounts"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {		
			$preProcessorQtyId	=	$p["preProcessorQtyId_".$i];
			$commission		=	$p["commission_".$i];	
			$selectRate		=	$p["rate_".$i];
			$actualAmount		=	$p["totalRate_".$i];
			
			$reEdited		= 	$p["reEdit_".$i];

			$preProcessorEntryId	=	$p["preProcessorEntryId_".$i];
			$idealYield		=	$p["idealYield_".$i];
			$paymentConfirmed	= 	$p["paymentConfirmed_".$i];
			$rmlot	= 	$p["rmlot_".$i];

			if ( ($reEdited=="" || $isAdmin==true || $reEdit==true) && $paymentConfirmed=='N') {
				$paid		= ($p["paid_".$i]=="")?N:$p["paid_".$i];
			} else {
				$paid = "";
			}

			if ($preProcessorQtyId!="" && $selectRate!="" && $actualAmount!="" && $rmlot=="") {
				$dailyPreProcessUpdateRec	=	$processorsaccountsObj->updatePreProcessPaidAmount($preProcessorQtyId, $selectRate, $actualAmount, $paid, $commission);

				$dailyPreProcessEntryRecUpdate = $processorsaccountsObj->updatePreProcessEntryRec($preProcessorEntryId, $idealYield);	
			}
			else if ($preProcessorQtyId!="" && $selectRate!="" && $actualAmount!="" && $rmlot!="") {
				$dailyPreProcessUpdateRec	=	$processorsaccountsObj->updatePreProcessPaidAmountRMLot($preProcessorQtyId, $selectRate, $actualAmount, $paid, $commission);

				$dailyPreProcessEntryRecUpdate = $processorsaccountsObj->updatePreProcessEntryRecRMLot($preProcessorEntryId, $idealYield);	
			}
		}
		if ($dailyPreProcessUpdateRec) {
			$updated		=	true;
			//$sessObj->createSession("displayMsg",$msg_succUpdateProcessrosAccounts);
			//$addMode=true;
			//$sessObj->createSession("nextPage",$url_afterUpdateSupplierSettlement);
		} else {
			$err	=	$msg_failUpdateProcessrosAccounts;
		}
		$dailyPreProcessUpdateRec	=	false;
	}
	
	#Listing all record based on Pre Processor Id
	if($g["selProcessor"]!="")	$processor	=	$g["selProcessor"]; 
	else $processor	=	$p["selProcessor"]; 

	if ($g["dateFrom"]!="")	$dateFrom 	= $g["dateFrom"];
	else if ($p["dateFrom"]) $dateFrom 	= $p["dateFrom"];
	else $dateFrom 	= date("d/m/Y");

	if ($g["dateTo"]!="")  $dateTo 	= $g["dateTo"];
	else if ($p["dateTo"]) $dateTo 	= $p["dateTo"];
	else $dateTo = date("d/m/Y");

	if($g["selProcessCode"]!="")  $selProcessCode 	= $g["selProcessCode"];
	else $selProcessCode = $p["selProcessCode"];
	
	if($g["selrmlotId"]!="")  $selRmlotId 	= $g["selrmlotId"];
	else $selRmlotId = $p["selrmlotId"];
	

	$fromDate		= mysqlDateFormat($dateFrom);
	$tillDate		= mysqlDateFormat($dateTo);

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="")		$pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="")	$pageNo=$g["pageNo"];
	else				$pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	if ($p["cmdSearch"]!="" || $updated==true || $pageNo!="" && ($processor!="" || $selProcessCode!="") ) {
		$dailyPreProcessRecords	= $processorsaccountsObj->fetchAllRecordsNew($fromDate, $tillDate, $processor, $offset, $limit, $selProcessCode,$selRmlotId);
		//$dailyPreProcessRecords	= $processorsaccountsObj->fetchAllRecords($fromDate, $tillDate, $processor, $offset, $limit, $selProcessCode);
		# Fetch All Records
		$getAllRecords	= $processorsaccountsObj->getAllPreProcessorRecordsNew($fromDate, $tillDate, $processor, $selProcessCode,$selRmlotId);	
		# Find Total Records in the date Range
		$numrows	= sizeof($getAllRecords);
	}

	#List all pre-Process Code
	$preProcessCodeRecords = $processorsaccountsObj->getDistinctProcesscode($fromDate, $tillDate);

	#List all pre-Processor
	$preProcessorRecords = 	$processorsaccountsObj->fetchDistinctPreProcessorRecords($fromDate, $tillDate, $selProcessCode);

	#List all rmlotid
	$rmlotRec=$processorsaccountsObj->fetchDistinctRMLotRecords($fromDate, $tillDate, $selProcessCode,$processor);
	## -------------- Pagination Settings II -------------------
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------	

	# Default Yield Tolerance
	$defaultYieldTolerance  = $displayrecordObj->getDefaultYieldTolerance();

	#For Finding Grand Total
	if (sizeof($getAllRecords)>0) {
			$grandTotalProcessRate = "";
			$grandTotalSettledAmount = "";
			$grandTotalDuesAmount	= "";
			foreach ($getAllRecords as $dpr) {
				$pDate			=	explode("-",$dpr[2]);
				$fishId			=	$dpr[1];
				$preProcessId		=	$dpr[4];
				$preProcessorId 	= 	$dpr[20];				
				$totalArrivalQty	=	$dpr[7];
				$totalPreProcessedQty	=	$dpr[8];
				$preProcessedQty	=	$dpr[13];
				#To Take the Rate & Commi
				list($rate, $commission, $criteria, $ppYieldTolerance) = $dailypreprocessObj->getPProcessorExpt($preProcessId, $preProcessorId);

				$lanCenterId 		=	$dpr[19];
				$processYieldRec = $dailypreprocessObj ->findYieldRec($preProcessId,$lanCenterId);
				$monthArray	=	array($processYieldRec[3],$processYieldRec[4],$processYieldRec[5],$processYieldRec[6],$processYieldRec[7],$processYieldRec[8],$processYieldRec[9],$processYieldRec[10],$processYieldRec[11],$processYieldRec[12],$processYieldRec[13],$processYieldRec[14]);
				$day	=	"";
				if($pDate[1]<10) $day =	$pDate[1]%10;
				else $day = $pDate[1];
				$idealYield = $monthArray[$day-1];
				###
				
				$selectCommission	=	$dpr[14];
				$selectRate			=	$dpr[15];
				$actualRate			=	$dpr[16];
				$paidStatus			=	$dpr[17];
				$displayCommission = "";	
				if($selectCommission!="" && $selectCommission!=0){
					$displayCommission	=	$selectCommission;
				} else {
					$displayCommission	=	$commission;
				}
				$changedRate = "";	
				if($selectRate!="" && $selectRate!=0){
					$changedRate	=	$selectRate;
				} else {
					$changedRate		=	$rate;
				}				
				$actualYield 	= $dpr[9];
				$diffYield	= number_format(($actualYield-$idealYield),2,'.','');
				$aYield	  = ($actualYield/100); 
				$IYield	  = ($idealYield/100);				
				//echo "$idealYield & $actualYield<br>";
				#Criteria Calculation 1=> From / 0=>To
				$totalPreProcessAmt = "";			
				# New Calc				
					$yieldTolerance = ($ppYieldTolerance!=0)?$ppYieldTolerance:$defaultYieldTolerance;				
					$ppQty = $totalPreProcessedQty;
					if ($criteria==1) {
					//if (From) and actual yield> ideal yield  then yield=actual yield
						if ($actualYield>$idealYield && $diffYield<$yieldTolerance) {
							$totalPreProcessAmt = ($ppQty/$aYield)*$changedRate+$ppQty*$displayCommission;			
						} else {
							$totalPreProcessAmt = ($ppQty/$IYield)*$changedRate+ $ppQty*$displayCommission;
							$finalYield	=	$idealYield;
						}					
					} else {
						$totalPreProcessAmt = $ppQty*$changedRate+$ppQty*$displayCommission;
						$finalYield	=	$idealYield;
					}
					//echo "<br>$changedRate, $displayCommission, $selPPCriteria=>$totalPreProcessAmt<br>";
					

					# new calc ends here

				
				$ratePerKg	=	 $totalPreProcessAmt/$totalPreProcessedQty;
				$amount		=	$preProcessedQty * $ratePerKg;									
				$totalRate = "";
				if($actualRate!="" && $actualRate!=0 && $paidStatus=='Y'){
					 $totalRate	= $actualRate;	
				} else {
					$totalRate		=	number_format($amount,2,'.','');
				}
				#Grand Total
				
				$grandTotalProcessRate	+=$totalRate;
				
				//$grandTotalProcessRate	+= $totalPreProcessedQty;
				
				if($paidStatus=='Y'){
					$checked	=	"Checked";
					$grandTotalSettledAmount +=	$totalRate;
				} else {
					$checked	=	"";
					$grandTotalDuesAmount	+=	$totalRate;
				}					
		}
	}
	#End

	# Display heading
	if ($editMode)	$heading	= $label_editProcessorSettlement;
	else		$heading	= $label_addProcessorSettlement;
	
	$ON_LOAD_PRINT_JS	= "libjs/processorsaccounts.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmProcessorsAccounts" action="ProcessorsAccounts.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="98%">
		<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){ ?><?=$err;?><? } ?></td>
        </tr>
        <?
		if( $editMode || $addMode )
		{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white"><!-- Form fields start -->
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
												<td colspan="2" height="10" ><input type="hidden" name="hidReceived" value="<?=$receivedBy?>"></td>
											</tr>
											<tr>
											<? if($editMode){?>
												<td colspan="2" align="center">&nbsp;&nbsp;</td>
											 <? } else{?>
												<td align="center" colspan="2">&nbsp;&nbsp;<? if($edit==true){?>
													<input type="submit" name="cmdAddProcessorsAccounts" class="button" value=" Save" onClick="return validateProcessorsSettlement(document.frmProcessorsAccounts);"><? }?>
												&nbsp;&nbsp;<? if($print==true){?>
													<input type="button" name="Submit" value="Print" class="button" onclick="return printWindow('PrintProcessorsAccounts.php?selProcessor=<?=$processor?>&selProcessCode=<?=$selProcessCode?>&selRmlotId=<?=$selRmlotId?>&fromDate=<?=$fromDate?>&tillDate=<?=$tillDate?>&offset=<?=$offset?>&limit=<?=$limit?>',700,600);" <? if(sizeof($dailyPreProcessRecords)==0) echo "disabled";?>><? }?></td>
												<? } ?>
											 </tr>
											  <input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
											<tr>
											  <td class="fieldName" nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
											  <td>&nbsp;</td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >
													<table>
														<tr>
															<td class="fieldName">Select Date : </td>
															<td class="fieldName">From:</td>
															<td nowrap>					
																<input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$dateFrom?>" onchange="submitForm('dateFrom','dateTo', frmProcessorsAccounts);"></td>
															<td class="fieldName">To:</td>
															<td nowrap>
															 <input type="text" id="dateTo" name="dateTo" size="8"  value="<?=$dateTo?>" onchange="submitForm('dateFrom','dateTo', frmProcessorsAccounts);">		
															</td>
															<td class="fieldName" nowrap="nowrap">Pre-Process Code : </td>
															<td>
				
																<select name="selProcessCode" id="selProcessCode" onchange="this.form.submit();">
																		<option value="">---Select All---</option>
																		<?
																		foreach ($preProcessCodeRecords as $pr) 
																		{
																			$processCodeId	=	$pr[0];
																			$processCode	=	stripSlash($pr[1]);
																			$selected	=	"";
																			if ($selProcessCode == $processCodeId) $selected = "selected";
																		?>
                  														<option value="<?=$processCodeId;?>" <?=$selected;?>><?=$processCode;?></option>
																		<?
					  													}
																		?>
																</select>
															</td>
															<td class="fieldName" nowrap="nowrap">Pre-Processor : </td>
															<td>
                       											<select name="selProcessor" id="selProcessor">
																	<option value="">--- Select ---</option>
																	<?php
																	foreach ($preProcessorRecords as $pr) 
																	{
																		$processorId	=	$pr[0];
																		$processorName	=	stripSlash($pr[1]);
																		$selected	=	"";
																		if ($processor == $processorId) $selected = "selected";
																		?>
																	<option value="<?=$processorId;?>" <?=$selected;?>><?=$processorName;?></option>
																	<?
																	}
																	?>
																</select>
															</td>
															<td class="fieldName" nowrap="nowrap">RM Lot Id : </td>
															<td>
                       											<select name="selrmlotId" id="selrmlotId">
																	<option value="">--- Select ---</option>
																	<?php
																	foreach ($rmlotRec as $rm) 
																	{
																		$rmlotIds	=	$rm[0];
																		$rmlotName	=	stripSlash($rm[1]);
																		$selected	=	"";
																		if ($selRmlotId == $rmlotIds) $selected = "selected";
																		?>
																	<option value="<?=$rmlotIds;?>" <?=$selected;?>><?=$rmlotName;?></option>
																	<?
																	}
																	?>
																</select>
															</td>
															<td>
																<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onclick="return validateSettlement(document.frmProcessorsAccounts);" />
															</td>
														</tr>
													</table>
												</td>
												<td valign="top">&nbsp;</td>
												<td valign="top">&nbsp;</td>
											</tr>
											<tr>
												<td colspan="3" nowrap class="fieldName" ></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap ></td>
												<td></td>
												<td class="fieldName"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap ></td>
												<td></td>
												<td class="fieldName"></td>
											</tr>
											<tr>
												<td colspan="3"  height="10" class="listing-item" >
													<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#999999">
													 <?	
													if(sizeof($dailyPreProcessRecords))
													{ $i	=	0;										 
													?>
														<tr bgcolor="#f2f2f2">
															<td colspan="13" bgcolor="#FFFFFF" class="listing-item" align="center">
																<table width="100%">
																	<tr>
																		<td align="right">
																			<table>
																				<tr>
																					<td class="listing-item">
																						<img src="images/x.gif" width="20" height="20"> - The entire Process Rates are not defined.									
																					</td>
																				</tr>
																			</table>									
																		</td>
																		<td align="right">
																			<table>
																				<tr>
																					<td class="listing-item"><a href="ProcessorSettlementSummary.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTo?>&selProcessor=<?=$processor?>" class="link1">View Settlement Summary</a>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<? if($maxpage>1){?>
																	<tr>
																		<td colspan="2" align="right" style="padding-right:10px;">
																			<div align="right">
																			<?php 				 			  
																			$nav  = '';
																			for($page=1; $page<=$maxpage; $page++)
																			{
																				if ($page==$pageNo)
																				{
																					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
																				}
																				else
																				{
																					$nav.= " <a href=\"ProcessorsAccounts.php?dateFrom=$dateFrom&dateTo=$dateTo&selProcessor=$processor&pageNo=$page&selProcessCode=$selProcessCode\" class=\"link1\">$page</a> ";
																				//echo $nav;
																				}
																			}
																			if ($pageNo > 1)
																			{
																				$page  = $pageNo - 1;
																				$prev  = " <a href=\"ProcessorsAccounts.php?dateFrom=$dateFrom&dateTo=$dateTo&selProcessor=$processor&pageNo=$page&selProcessCode=$selProcessCode\"  class=\"link1\"><<</a> ";
																			}
																			else
																			{
																				$prev  = '&nbsp;'; // we're on page one, don't print previous link
																				$first = '&nbsp;'; // nor the first page link
																			}
																			if ($pageNo < $maxpage)
																			{
																				$page = $pageNo + 1;
																				$next = " <a href=\"ProcessorsAccounts.php?dateFrom=$dateFrom&dateTo=$dateTo&selProcessor=$processor&pageNo=$page&selProcessCode=$selProcessCode\"  class=\"link1\">>></a> ";
																			}
																			else
																			{
																				$next = '&nbsp;'; // we're on the last page, don't print next link
																				$last = '&nbsp;'; // nor the last page link
																			}
																			// print the navigation link
																			$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
																			echo $first . $prev . $nav . $next . $last . $summary; 
																			?>
																		</div>
																	</td>
																</tr>
																<? }?>
															</table>
														</td>
													</tr>
													<? if($err!="" ){?>
													<tr bgcolor="#f2f2f2">
														<td colspan="13" class="err1" align="center"><?=$err;?></td>
													</tr>
													<? }?>
													<tr bgcolor="#f2f2f2" align="center">
														<td class="listing-head">No</td>
														<td class="listing-head">RM Lot Id</td>
														<td class="listing-head">Fish</td>
														<td class="listing-head">Pre-Process<br /> Code </td>
														<td class="listing-head">Base <br />Rate(Rs.) </td>
														<td class="listing-head">Base<br /> Commi (Rs.) </td>
														<td class="listing-head">Pre Processed <br />Quantity</td>
														<td class="listing-head">Final<br />Yield<br> (%)</td>
														<!--td class="listing-head">Amount(Rs.)</td-->
														<td class="listing-head">Commi</td>
														<td class="listing-head">Rate</td>
														<td class="listing-head">Total</td>
														<td class="listing-head">Setld</td>
														<td class="listing-head">Setld date</td>
													</tr>
													<?php
													$settledAmount = "";
													$duesAmount    = "";
													$totalProcessRate = "";
													$grandTotalPreProcessesQty = 0;
													foreach ($dailyPreProcessRecords as $dpr) 
													{
														$i++;
														$dailyPreProcessEntryId =  $dpr[3];
														$selectDate = $dpr[2];
														$displayDate = "";
														if ($prevSelectDate!=$selectDate) {
															$displayDate = dateFormat($selectDate);
														}
														$pDate		=	explode("-",$dpr[2]);
														$setldDate	=	$dpr[18];
														$processorSettledDate = "";
														if ($setldDate!=0) 
														{
															$array			=	explode("-",$setldDate);
															$processorSettledDate	=	$array[2]."/".$array[1]."/".$array[0];
														}
														$fishId			=	$dpr[1];
														$fishRec		=	$fishmasterObj->find($fishId);
														$fishName		=	$fishRec[1];
														$preProcessId		=	stripSlash($dpr[4]);

														//$processRec		=	$processObj->find($preProcessId);
														//$preProcessCode	=	$processRec[7];
														$preProcessCode 	= 	$dpr[22];
														//check here preprocess code 
														//$is_activepreProcessCode = true;
														//rekha updated code dated: 19 june 2019 
														$is_activepreProcessCode= $processorsaccountsObj->chkActiveCode($fishId,$preProcessCode,$preProcessId);
														
														
														//echo($is_activepreProcessCode);
														//echo("<br>");
														
														//end code 
														$preProcessorQtyId	=	$dpr[12];
														$totalArrivalQty	=	$dpr[7];
														$totalPreProcessedQty	=	$dpr[8];
														$actualYield		=	$dpr[9];
														$preProcessedQty	=	$dpr[13];
														
														if($is_activepreProcessCode){
															$grandTotalPreProcessesQty += $preProcessedQty; // Find Total Pre Processed Qty
														}
														$preProcessorId = $dpr[20];

														#To Take the Rate & Commi		
														list($rate, $commission, $criteria, $ppYieldTolerance) = $dailypreprocessObj->getPProcessorExpt($preProcessId, $preProcessorId);

														$lanCenterId 		=	$dpr[19];
														######################
														$processYieldRec = $dailypreprocessObj ->findYieldRec($preProcessId, $lanCenterId);
														
														$monthArray	= array($processYieldRec[3], $processYieldRec[4], $processYieldRec[5], $processYieldRec[6], $processYieldRec[7], $processYieldRec[8], $processYieldRec[9], $processYieldRec[10], $processYieldRec[11], $processYieldRec[12], $processYieldRec[13], $processYieldRec[14]);
														$day	=	"";
														if ($pDate[1]<10) $day = $pDate[1]%10;
														else $day = $pDate[1];

														$idealYield 	= $monthArray[$day-1];
														$diffYield	= number_format(($actualYield-$idealYield),2,'.','');
														#################		
														$selectCommission	=	$dpr[14];
														$selectRate		=	$dpr[15];
														$actualRate		=	$dpr[16];
														$paidStatus		=	$dpr[17];
																			
														$displayCommission = "";	
														if ($selectCommission!="" && $selectCommission!=0) {
															$displayCommission	=	$selectCommission;
														} else {
															$displayCommission	=	$commission;
														}
																		
														$changedRate = "";	
														if ($selectRate!="" && $selectRate!=0) {
															$changedRate	=	$selectRate;
														} else {
															$changedRate	=	$rate;
														}
														
														$aYield	  = ($actualYield/100); 
														$IYield	  = ($idealYield/100);
														

														#Criteria Calculation 1=> From / 0=>To
														$totalPreProcessAmt = "";
														$finalYield 	= "";

														# New Calc
														$yieldTolerance = ($ppYieldTolerance!=0)?$ppYieldTolerance:$defaultYieldTolerance;
														$ppQty = $totalPreProcessedQty;
														if ($criteria==1) {
														//if (From) and actual yield> ideal yield  then yield=actual yield
															if ($actualYield>$idealYield && $diffYield<$yieldTolerance) {
																$totalPreProcessAmt = ($ppQty/$aYield)*$changedRate+$ppQty*$displayCommission;
																$finalYield	=	$actualYield;
															} else {
																$totalPreProcessAmt = ($ppQty/$IYield)*$changedRate+ $ppQty*$displayCommission;
																$finalYield	=	$idealYield;
															}					
														} else {
															$totalPreProcessAmt = $ppQty*$changedRate+$ppQty*$displayCommission;
															$finalYield	=	$idealYield;
														}				

														# new calc ends here	
														$ratePerKg	=	 $totalPreProcessAmt/$totalPreProcessedQty;
														$amount		=	$preProcessedQty * $ratePerKg;		
														
														$totalRate = "";
														if ($actualRate!="" && $actualRate!=0 && $paidStatus=='Y') {
															$totalRate	= $actualRate;	
														} else {
															$totalRate	= number_format($amount,2,'.','');
														}
														//echo "<br>$preProcessedQty=$totalRate==>$actualRate:$amount:::::$selPPCriteria=>$rate, $commission, $criteria";
														# Column Total
														
														if($is_activepreProcessCode){
															$totalProcessRate	+= $totalRate;
														}
														
														if ($paidStatus=='Y') {
															$checked	=	"Checked";
															$settledAmount	= $settledAmount +	$totalRate;
														} else {
															$checked	=	"";
															$duesAmount	= $duesAmount +	$totalRate;
														}

														$paymentConfirmed	= $dpr[21];
														$disabled = "";
														$edited	  = "";					
														if ($paymentConfirmed=='Y' || ($paidStatus=='Y' && $isAdmin==false && $reEdit==false)) {
															$disabled = "readonly";
															$edited	  = 1;
														}
														
														
														($dpr[25]!='' && $dpr[25]!='0')?$rmlotName = $dpr[25]:$rmlotName = "";
														($dpr[24]!='' && $dpr[24]!='0')?$rmlotId = $dpr[24]:$rmlotId = "";
														
														if($is_activepreProcessCode){
														//if(true){
														?>
														<tr bgcolor="#FFFFFF">
															<td class="listing-item"><input type="hidden" name="preProcessorEntryId_<?=$i;?>" value="<?=$dailyPreProcessEntryId?>">&nbsp;&nbsp;<input type="hidden" name="preProcessorQtyId_<?=$i;?>" value="<?=$preProcessorQtyId?>"><?=$displayDate?><?//(($pageNo-1)*$limit)+$i?></td>
															<td class="listing-item">&nbsp;&nbsp;<?=$rmlotName?>
															<input type="hidden" name="rmlot_<?=$i?>" id="rmlot_<?=$i?>" value="<?=$rmlotId?>"/></td>
															<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$fishName?></td>
															<td class="listing-item">&nbsp;&nbsp;<?=$preProcessCode?></td>
															<td class="listing-item" align="right" style="padding-right:10px;"><? if($rate==""){?><img src="images/x.gif" width="20" height="20"><? } else { echo $rate;}?></td>
															<td class="listing-item" align="right" style="padding-right:10px;"><?=$commission?></td>
															<td class="listing-item" align="right">
																<input type="hidden" name="totalArrivalQty_<?=$i?>" id="totalArrivalQty_<?=$i?>" value="<?=$totalArrivalQty?>">
																<input type="hidden" name="totalPreProcessedQty_<?=$i?>" id="totalPreProcessedQty_<?=$i?>" value="<?=$totalPreProcessedQty?>">
																<input type="hidden" name="preProcessedQty_<?=$i?>" id="preProcessedQty_<?=$i?>" value="<?=$preProcessedQty?>">
																<input type="hidden" name="preProcessRate_<?=$i?>" id="preProcessRate_<?=$i?>" value="<?=$rate?>">
																<input type="hidden" name="preProcessCommission_<?=$i?>" id="preProcessCommission_<?=$i?>" value="<?=$commission?>">
																<input type="hidden" name="criteria_<?=$i?>" id="criteria_<?=$i?>" value="<?=$criteria?>">
																<input type="hidden" name="idealYield_<?=$i?>" id="idealYield_<?=$i?>" value="<?=$idealYield?>">
																<input type="hidden" name="actualYield_<?=$i?>" id="actualYield_<?=$i?>" value="<?=$actualYield?>">
																<input type="hidden" name="diffYield_<?=$i?>" id="diffYield_<?=$i?>" value="<?=$diffYield?>">
																<input type="hidden" name="ppYieldTolerance_<?=$i?>" id="ppYieldTolerance_<?=$i?>" value="<?=$ppYieldTolerance?>">
																<?=$preProcessedQty?> &nbsp;&nbsp;
															</td>
															<td class="listing-item" nowrap align="right"><?=$finalYield?></td>
															<td class="listing-item" nowrap align="right">
																<input type="text" name="commission_<?=$i;?>" id="commission_<?=$i;?>" value="<?=$displayCommission?>" size="2" style="text-align:right;" onkeyup="return actualValue(document.frmProcessorsAccounts);" <?=$disabled?> autocomplete="off">&nbsp;&nbsp;</td>
															<td class="listing-item" nowrap align="right">
																<input type="text" name="rate_<?=$i;?>" id="rate_<?=$i;?>" value="<?=$changedRate?>" size="2" style="text-align:right" onkeyup="return actualValue(document.frmProcessorsAccounts);" <?=$disabled?> autocomplete="off">&nbsp;&nbsp;</td>
															<td class="listing-item" nowrap align="right" style="padding-left:2px; padding-right:2px;"><input type="text" name="totalRate_<?=$i;?>" id="totalRate_<?=$i;?>" value="<?=$totalRate?>" size="6" style="text-align:right; border:none;" readonly>&nbsp;&nbsp;</td>
															<td class="listing-item" nowrap align="center">
																<input name="paid_<?=$i;?>" type="checkbox" id="paid_<?=$i;?>" value="Y"    <?=$checked?> class="chkBox" <?=$disabled?>>
																<input type="hidden" name="reEdit_<?=$i;?>" value="<?=$edited?>">
																<input name="paymentConfirmed_<?=$i;?>" type="hidden" id="paymentConfirmed_<?=$i;?>" value="<?=$paymentConfirmed?>">   
															</td>
															<td class="listing-item" nowrap align="center"><?=$processorSettledDate?></td>
														</tr>
														 <?
															$prevSelectDate = $selectDate;
														} //end if
															}
														?>
														<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
														<input type="hidden" name="defaultYieldTolerance" id="defaultYieldTolerance" value="<?=$defaultYieldTolerance?>">
														<tr bgcolor="white">
															<td height="10" colspan="6" align="right" class="listing-head">Total:</td>
															<td height="10" align="right" class="listing-item"><b><? echo number_format($grandTotalPreProcessesQty,2);?></b>&nbsp;&nbsp;</td>
															<td height="10" colspan="3" align="right" class="listing-head"></td>
															<td height="10" align="right" style="padding-left:2px; padding-right:2px;"><input name="totalProcessRate" type="text" id="totalProcessRate" size="8" style="border:none; text-align:right" value="<? echo number_format($totalProcessRate,2);?>" readonly="true"></td>
															<td height="10" align="center">&nbsp;</td>
															<td align="center">&nbsp;</td>
														  </tr>
														  <tr bgcolor="white">
															<td height="10" colspan="10" align="right" class="listing-head">Grand Total: </td>
															<td height="10" align="right" style="padding-left:2px; padding-right:2px;" class="listing-item"><b><?=number_format($grandTotalProcessRate,2,'.',',');?><b></td>
															<td height="10" align="center">&nbsp;</td>
															<td align="center">&nbsp;</td>
														  </tr>
														  <? if($maxpage>1){?>
														  <tr bgcolor="#FFFFFF">
															<td colspan="13" style="padding-right:10px;">
																<div align="right">
																<?php 				 			  
																$nav  = '';
																for($page=1; $page<=$maxpage; $page++)
																{
																	if ($page==$pageNo)
																	{
																		$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
																	}
																	else
																	{
																		$nav.= " <a href=\"ProcessorsAccounts.php?dateFrom=$dateFrom&dateTo=$dateTo&selProcessor=$processor&pageNo=$page&selProcessCode=$selProcessCode\" class=\"link1\">$page</a> ";
																	//echo $nav;
																	}
																}
																if ($pageNo > 1)
																{
																	$page  = $pageNo - 1;
																	$prev  = " <a href=\"ProcessorsAccounts.php?dateFrom=$dateFrom&dateTo=$dateTo&selProcessor=$processor&pageNo=$page&selProcessCode=$selProcessCode\"  class=\"link1\"><<</a> ";
																}
																else
																{
																	$prev  = '&nbsp;'; // we're on page one, don't print previous link
																	$first = '&nbsp;'; // nor the first page link
																}

																if ($pageNo < $maxpage)
																{
																	$page = $pageNo + 1;
																	$next = " <a href=\"ProcessorsAccounts.php?dateFrom=$dateFrom&dateTo=$dateTo&selProcessor=$processor&pageNo=$page&selProcessCode=$selProcessCode\"  class=\"link1\">>></a> ";
																}
																else
																{
																	$next = '&nbsp;'; // we're on the last page, don't print next link
																	$last = '&nbsp;'; // nor the last page link
																}
				
																// print the navigation link
																$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
																echo $first . $prev . $nav . $next . $last . $summary; 
																?>
																<input type="hidden" name="pageNo" value="<?=$pageNo?>">
																</div>
															</td>
														</tr>
														 <? }?>
													 <? } else {?>
														<tr bgcolor="white">
															<td colspan="13"  class="err1" height="10" align="center"><?=$msgNoRecords;?>                                    </td>
														</tr>
													 <? } 
													 if( sizeof($dailyPreProcessRecords)){?>
														<tr bgcolor="#FFFFFF">
															<td colspan="13" height=1>
																<table>
																	<tr>
																	  <td class="fieldName">Already Settled</td>
																	  <td><input name="settledAmount" type="text" id="settledAmount" size="5" readonly value="<?=$grandTotalSettledAmount?>" style="text-align:right" /></td>
																	  <td class="fieldName">Total Dues </td>
																	  <td><input name="duesAmount" type="text" id="duesAmount" size="5" readonly value="<?=$grandTotalDuesAmount?>" style="text-align:right" /></td>
																	  <td class="fieldName">Net Payable</td>
																	  <td class="fieldName"><input name="netPayable" type="text" id="netPayable" size="5" readonly  style="text-align:right" value="<?=$grandTotalDuesAmount?>" /></td>
																	</tr>
																</table>
															</td>
														</tr>
														<? }?>
													</table>
												</td>
											</tr>
											<tr>
											  <td colspan="2" align="center">&nbsp;</td>
											  <td align="center" colspan="2">&nbsp;</td>
											</tr>
											<tr>
											  <? if($editMode){?>
												<td colspan="2" align="center">&nbsp;&nbsp;</td>
											  <? } else{ ?>
												<td align="center" colspan="2">&nbsp;&nbsp;<? if($edit==true){?>
												  <input type="submit" name="cmdAddProcessorsAccounts" class="button" value=" Save " onClick="return validateProcessorsSettlement(document.frmProcessorsAccounts);"><? }?>
												&nbsp;&nbsp; <? if($print==true){?>
												 <input type="button" name="Submit" value="Print" class="button" onclick="return printWindow('PrintProcessorsAccounts.php?selProcessor=<?=$processor?>&selProcessCode=<?=$selProcessCode?>&selRmlotId=<?=$selRmlotId?>&fromDate=<?=$fromDate?>&tillDate=<?=$tillDate?>&offset=<?=$offset?>&limit=<?=$limit?>',700,600);" <? if(sizeof($dailyPreProcessRecords)==0) echo "disabled";?>><? }?></td>
												<input type="hidden" name="cmdAddNew" value="1" />
											  <? } ?>
											</tr>
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
        <?
		}
		?>
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
			inputField  : "dateTo",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateTo", 
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
