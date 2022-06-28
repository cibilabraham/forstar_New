<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$checked	= "";

	#-------------------Admin Checking--------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
	
	//------------  Checking Access Control Level  ----------------
	$add		= false;
	$edit		= false;
	$del		= false;
	$print		= false;
	$confirm	= false;
	$reEdit 	= false;
	
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
	//----------------------------------------------------------

	if ($p["selProcessor"]=="") {
		$processor = $g["selProcessor"];
	} else {
		$processor = $p["selProcessor"]; 
	}

	# select record between selected date
	if ($p["supplyFrom"]=="" && $p["supplyTill"]=="") {
		$dateFrom = $g["supplyFrom"];
		$dateTill = $g["supplyTill"];
	} else {
		$dateFrom = $p["supplyFrom"];
		$dateTill = $p["supplyTill"];
	}

	$Date1	  = explode("/",$dateFrom);
	$fromDate = $Date1[2]."-".$Date1[1]."-".$Date1[0];

	$Date2		= explode("/",$dateTill);
	$tillDate	= $Date2[2]."-".$Date2[1]."-".$Date2[0];

	#Update Paid Record
	if ( $p["cmdProcessorPayment"]!="") {
		$rowCount	= $p["hidRowCount"];
		$totalPayingAmount = $p["totalpaidAmount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$entryDate	= $p["entryDate_".$i];
			$reEdited	= $p["reEdit_".$i];
			if ($reEdited=="" || $isAdmin==true || $reEdit==true) {
				$paid	= ($p["paid_".$i]=="")?N:$p["paid_".$i];
			} else {
				$paid	= "";
			}
			
			if ($entryDate!="" && $paid!="") {
				#extracting All Records from the Grouped records based on date
				$settledRecords = $processorsettlementsummaryObj->getAllSettledRecords($fromDate, $tillDate, $processor, $entryDate);
				foreach ($settledRecords as $sr) {
					$processorEntryId = $sr[0];
					$updateProcessorPayment = $processorsettlementsummaryObj->updateProcessorPayment($processorEntryId, $paid);				
				}				
			}
		}
		if ($updateProcessorPayment!="" && ($totalPayingAmount!="" || $totalPayingAmount!=0)) {
			header("Location:ProcessorsPayments.php?processor=$processor&totalPayingAmount=$totalPayingAmount");
		}		
	}

	#Select the records based on date
	if ($dateFrom!="" && $dateTill!="") {
		#For Pre-Processor Lisitng
		$preProcessorRecords = 	$processorsettlementsummaryObj ->fetchDistinctPreProcessorRecords($fromDate, $tillDate);

		$processorSettlementRecords	= $processorsettlementsummaryObj -> fetchProcessorSettlementRecords($fromDate, $tillDate, $processor);
	}

	# Display heading
	if ($editMode) {
		$heading	= $label_editPurchaseSettlement;
	} else {
		$heading	= $label_addPurchaseSettlement;
	}

	$ON_LOAD_PRINT_JS	= "libjs/processorsettlementsummary.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmProcessorSettlementSummary" action="ProcessorSettlementSummary.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="98%">
		<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="50%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Pre-Processor Settlement Summary </td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2"  align="center">
										<table cellpadding="0"  width="99%" cellspacing="0" border="0" align="center">
											<tr> 
												<td colspan="2" height="5"></td>
											</tr>
											<tr> 
											<? if($editMode){?>
											<?} else{?>
												<td colspan="4" align="center"><? if($edit==true){?><input name="cmdProcessorPayment" type="submit" class="button" id="cmdProcessorPayment" onClick="return validateProcessorSettlementSummary(document.frmProcessorSettlementSummary);" value=" Save "><? }?></td>
											<?} ?>
											</tr>
											<input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
											<tr>
												<td colspan="3" nowrap height="5"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >&nbsp;</td>
												<td colspan="2" align="center">
													<table width="250">
														<tr> 
															<td class="fieldName"> From</td>
															<td>
																<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" onchange="this.form.submit();">
															</td>
															<td class="fieldName">To</td>
															<td>
																<input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" onChange="this.form.submit();">
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr> 
												<td class="fieldName" nowrap >&nbsp;</td>
												<td colspan="2" align="center">
													<table width="250" cellpadding="0" cellspacing="0">
														<tr>
															<td class="fieldName"></td>
															<td></td>
														</tr>
														<tr> 
															<td class="fieldName" nowrap>Pre-Processor:</td>
															<td>
																<select name="selProcessor" id="selProcessor" onchange="this.form.submit();">
																	<option value="">--- Select ---</option>
																	<? 
																	foreach ($preProcessorRecords as $pr) {
																	$processorId	= $pr[0];
																	$processorName	= stripSlash($pr[1]);
																	$processorCode	= stripSlash($pr[2]);
																	$selected	= "";
																	if ($processor == $processorId || $recordProcessorId == $processorId) {
																		$selected	=	"selected";
																	}
																	?>
																	<option value="<?=$processorId;?>" <?=$selected;?>><?=$processorName;?></option>
																	<?
																	}
																	?>
																  </select>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<? 
											if (sizeof($processorSettlementRecords)>0) 
											{
												$i = 0;
											?>
											<tr>
												<td colspan="4" align="center">
													<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
														<tr bgcolor="#f2f2f2" align="center">
															<th nowrap="nowrap" class="fieldName" style="padding-left:5px; padding-right:5px;" width="20%"><span class="fieldName" style="padding-left:5px; padding-right:5px;">Date</span></th> 
															<th class="fieldName" style="padding-left:5px; padding-right:5px;" width="20%">Total</th>
															<th class="fieldName" style="padding-left:5px; padding-right:5px;" width="20%">Paid</th>
														</tr>
														<?
														$totalCost	= 	"";
														foreach ($processorSettlementRecords as $psr) 
														{
														$i++;
														
														$entryDate		=	$psr[2];		
														$array			=	explode("-",$psr[2]);
														$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
														
														$checkAllSettled = $processorsettlementsummaryObj->checkAllRecordsSettled($fromDate, $tillDate, $processor, $entryDate);
														
														if (!$checkAllSettled) {		
															$processorAmount	=	$psr[16];
															$totalCost		+=	$processorAmount;
															
															$isPaid = $psr[19];
															
															$checked = "";
															if ($isPaid=='Y') {
																$checked = "Checked";
																$paidAmount += $processorAmount;
															} else {
																$unpaidAmount += $processorAmount;
															}
															$disabled = "";
															$edited	  = "";
															if ($isPaid=='Y' && $isAdmin==false && $reEdit==false) {
																$disabled = "readonly";
																$edited	  = 1;
															}
														
														?>
														<tr bgcolor="#FFFFFF">
															<td height="25" nowrap class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$enteredDate?>
																<input name="entryDate_<?=$i?>" type="hidden" id="entryDate_<?=$i?>" value="<?=$entryDate;?>" /></td> 
															<td class="listing-item" align="right"><?=$processorAmount?>&nbsp;&nbsp;<input type="hidden" name="payingAmount_<?=$i;?>" id="payingAmount_<?=$i;?>" value="<?=$processorAmount?>"> </td>
															<td align="center"><input name="paid_<?=$i;?>" type="checkbox" id="paid_<?=$i;?>" value="Y"  <?=$checked?> class="chkBox" onclick="paidProcessorAmount()"><input type="hidden" name="alreadyPaid_<?=$i;?>" id="alreadyPaid_<?=$i;?>" value="<? if($checked) echo 'Y';?>"><input type="hidden" name="reEdit_<?=$i;?>" value="<?=$edited?>"></td>
														</tr>
														<? } else {   ?>
														<tr>
															<td height="25" nowrap class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$enteredDate?></td>
															<td colspan="2" class="err1" style="padding-left:5px; padding-right:5px;">Settlements are pending </td>
														</tr>
														<? } }?>
														<tr bgcolor="#FFFFFF">
															<td class="listing-item" nowrap>&nbsp;</td> 
															<td class="listing-item" align="right" nowrap="nowrap"><strong> 
															<? echo number_format($totalCost,2);?></strong>&nbsp;&nbsp;</td>
															<td>&nbsp;</td>
														</tr>			  
													</table>
												</td>
												<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											</tr>
											<? }?>
											<tr> 
												<td colspan="4" align="center" class="err1"><? if(sizeof($processorSettlementRecords)<=0 && $processor!=""){ echo $msgNoSettlement;}?></td>
											</tr>
											<? 
											if (sizeof($processorSettlementRecords)>0) {
											?>
											<tr>
												<td colspan="4" align="center">
													<table>
														<tr>
															<td class="fieldName"> Paid:</td>
															<td class="listing-item"><strong><? echo number_format($paidAmount,2);?></strong>&nbsp;&nbsp;</td>
															<td class="fieldName">Unpaid: </td>
															<td class="listing-item"><strong><? echo number_format($unpaidAmount,2);?></strong></td>
														</tr>
													</table>
													<input type="hidden" name="totalpaidAmount" id="totalpaidAmount">
												</td>
											</tr>
											<? }?>
											<tr> 
											<? if($editMode){?>
											<? } else { ?>
												<td colspan="4" align="center"><? if($edit==true){?><input name="cmdProcessorPayment" type="submit" class="button" id="cmdProcessorPayment" onClick="return validateProcessorSettlementSummary(document.frmProcessorSettlementSummary);" value=" Save "><? }?></td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
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
