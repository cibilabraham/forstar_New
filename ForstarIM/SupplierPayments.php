<?php
	require("include/include.php");
	require_once("lib/supplierpayments_ajax.php");

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$supplierAmtPaid	= false;
	$redirectLocation = "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"]."&supplierFilter=".$p["supplierFilter"];

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
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	//Coming from Setlement Summary
	$paidSupplierId = $g["supplier"];
	$paidAmount = $g["totalPayingAmount"];
		if ($paidSupplierId!="") 	{
			$paidRecords = $supplierpaymentsObj->getPaidRecords($paidSupplierId);
			$advanceAmount = 0;
			$alreadyPaidAmount = 0;
			foreach($paidRecords as $pr){
				$amountPaid = $pr[2];
				$paidMode   = $pr[3];
				if($paidMode=='A'){
					$advanceAmount += $amountPaid;
				} else {
					$alreadyPaidAmount +=$amountPaid;
				}
		}
		$balanceAmount = $advanceAmount - ($alreadyPaidAmount+$paidAmount);
	}

	# Add New Supplier Payments
	
	if( $p["cmdAddNew"]!="" || $paidSupplierId!=""){
		$addMode	= true;
	}

	if ($paidSupplierId) {
		$paymentType 	= $g["paymentType"];
		$sFromDate	= $g["setldFrom"];
		$sToDate	= $g["setldTill"];		
		$billingCompany = $g["setldBillingCompany"];
		$selChallan	= base64_decode($g["setldChallan"]);
		$supplierAmtPaid = true;
	}

	if( $p["cmdCancel"]!="" ){
		$addMode = false;
		$editMode = false;
		$paidSupplierId = "";
		$sFromDate ="";
		$sToDate = "";
		$billingCompany = "";
		$selChallan = "";
		$supplierAmtPaid = false;
	}

	# Add Supplier Payments
	if ($p["cmdAdd"]!="") {
		$supplier	= $p["supplier"];
		$chequeNo	= $p["chequeNo"]; //CHQ/DD NO
		$amount		= $p["amount"];
		$paymentMode	= ($p["paidSupplier"]=="")?A:D; //A-advance - D-direct payment
		$paymentDate	= mysqlDateFormat($p["paymentDate"]);
		$bankName	= addSlash(trim($p["bankName"]));
		//$ddNo		= addSlash(trim($p["ddNo"]));
		$payableAt	= addSlash(trim($p["payableAt"]));

		$paymentMethod  = $p["paymentMethod"];  //CH [OR] DD 		
		$paymentType	= $p["paymentType"];	//Advance/Settl
		$paymentReason	= addSlash(trim($p["paymentReason"]));
		$accountEntryNo	= addSlash(trim($p["accountEntryNo"]));

		if ($paymentType=='S') {
			$dateType	= $p["dateType"]; // WT Date/Supplier
			$fromDate	= mysqlDateFormat($p["fromDate"]);
			$toDate		= mysqlDateFormat($p["toDate"]);
			$selChallan	= implode(",", $p["selChallan"]);
			$billingCompany = $p["billingCompany"];
			$selSettlementDate = mysqlDateFormat($p["selSettlementDate"]);
		}

		if ($supplier!="" && $amount!="") {
			$supplierPaymentsRecIns	= $supplierpaymentsObj->addSupplierPayments($supplier, $chequeNo, $amount, $paymentMode, $paymentDate, $bankName, $userId, $payableAt, $paymentMethod, $paymentType, $paymentReason, $accountEntryNo, $dateType, $fromDate, $toDate, $selChallan, $billingCompany, $selSettlementDate);
			if ($supplierPaymentsRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddSupplierPayment);
				$sessObj->createSession("nextPage",$url_afterAddSupplierPayment.$redirectLocation);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddSupplierPayment;
			}
			$supplierPaymentsRecIns	=	false;
		}
		$addMode=false;
	}


	# Edit upplier Payments
	if ($p["editId"]!="") {
		$editMode		= true;
		$editId			= $p["editId"];
		$supplierPaymentsRec	= $supplierpaymentsObj->find($editId);

		$supplierPaymentsId	= $supplierPaymentsRec[0];
		$paidSupplierId		= $supplierPaymentsRec[1];
		$cheque			= $supplierPaymentsRec[2];
		$paidAmount		= $supplierPaymentsRec[3];
		$enteredDate 		= dateFormat($supplierPaymentsRec[4]);
		$bankName		= stripSlash($supplierPaymentsRec[5]);		
		$payableAt		= stripSlash($supplierPaymentsRec[6]);

		$paymentMethod  = $supplierPaymentsRec[7];  //CH [OR] DD 		
		$paymentType	= $supplierPaymentsRec[8];	//Advance/Settl
		$paymentReason	= $supplierPaymentsRec[9];
		$accountEntryNo	= $supplierPaymentsRec[10];
		$dateType	= $supplierPaymentsRec[11]; // WT Date/Supplier
		$sFromDate	= dateFormat($supplierPaymentsRec[12]);
		$sToDate	= dateFormat($supplierPaymentsRec[13]);
		$selChallan	= $supplierPaymentsRec[14];
		$billingCompany = $supplierPaymentsRec[15];
		$selSettlementDate = ($supplierPaymentsRec[16]!="0000-00-00")?dateFormat($supplierPaymentsRec[16]):"";
	}	

	# Save Change	
	if ($p["cmdSaveChange"]!="") {
		$supplierPaymentsId	= $p["hidSupplierPaymentsId"];
		
		$supplier	= $p["supplier"];
		$chequeNo	= $p["chequeNo"];
		$amount		= $p["amount"];
		$paymentDate	= mysqlDateFormat($p["paymentDate"]);
		$bankName	= addSlash($p["bankName"]);
		//$ddNo		= addSlash(trim($p["ddNo"]));
		$payableAt	= addSlash(trim($p["payableAt"]));
		$paymentMethod  = $p["paymentMethod"];  //CH [OR] DD 		
		$paymentType	= $p["paymentType"];	//Advance/Settl
		$paymentReason	= $p["paymentReason"];
		$accountEntryNo	= $p["accountEntryNo"];

		if ($paymentType=='S') {
			$dateType	= $p["dateType"]; // WT Date/Supplier
			$fromDate	= mysqlDateFormat($p["fromDate"]);
			$toDate		= mysqlDateFormat($p["toDate"]);
			$selChallan	= implode(",", $p["selChallan"]);
			$billingCompany = $p["billingCompany"];
			$selSettlementDate = mysqlDateFormat($p["selSettlementDate"]);
		} else {
			$dateType	= "";
			$fromDate	= "";
			$toDate		= "";
			$selChallan	= "";
			$billingCompany = "";
			$selSettlementDate = "";
		}

		if ($supplierPaymentsId!="" && $supplier!="" && $amount!="") {
			$supplierPaymentsRecUptd =	$supplierpaymentsObj->updateSupplierPayments($supplierPaymentsId, $supplier, $chequeNo, $amount, $paymentDate, $bankName, $payableAt, $paymentMethod, $paymentType, $paymentReason, $accountEntryNo, $dateType, $fromDate, $toDate, $selChallan, $billingCompany, $selSettlementDate);		
		}
		if ($supplierPaymentsRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateSupplierPayment);
			$sessObj->createSession("nextPage",$url_afterUpdateSupplierPayment.$redirectLocation);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateSupplierPayment;
		}
		$supplierPaymentsRecUptd = false;
	}

	# Delete Supplier Payment	
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$supplierPaymentsId	= $p["delId_".$i];
			if ($supplierPaymentsId!="") {
				// Need to chk when del
				$supplierPaymentsRecDel =	$supplierpaymentsObj->deleteSupplierPayments($supplierPaymentsId);
			}
		}
		if ($supplierPaymentsRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSupplierPayment);
			$sessObj->createSession("nextPage",$url_afterDelSupplierPayment.$redirectLocation);
		} else {
			$errDel		=	$msg_failDelSupplierPayment;
		}
	}
	
	
	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Records
	
	# select records between selected date
	if ($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
		$supplierFilterId = $g["supplierFilter"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
		$supplierFilterId = $p["supplierFilter"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
		$supplierFilterId = "";
	}
	
	# Resettting offset values
	if ($p["hidSupplierFilterId"]!=$p["supplierFilter"]) {		
		$offset = 0;
		$pageNo = 1;		
	}
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$supplierPaymentsRecords = $supplierpaymentsObj->supplierPaymentsRecPagingFilter($fromDate, $tillDate, $offset, $limit, $supplierFilterId);
		$numrows	=  sizeof($supplierpaymentsObj->supplierPaymentsRecFilter($fromDate, $tillDate, $supplierFilterId));
	}	
	$supplierPaymentsRecordsSize = sizeof($supplierPaymentsRecords);
	
	## -------------- Pagination Settings II -------------------
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	
	#List all Main Suppliers
	//$supplierRecords	= $supplierMasterObj->fetchAllRecords("FRN");
	$supplierRecords	= $supplierMasterObj->fetchAllRecordsActivesupplier("FRN");


	if ($addMode)		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	# Display heading
	if ($editMode)	$heading = $label_editSupplierPayment;
	else		$heading = $label_addSupplierPayment;
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	

	$ON_LOAD_PRINT_JS	= "libjs/supplierpayments.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSupplierpayments" action="SupplierPayments.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%">
		<tr>
		  <td height="30" align="center" class="err1"><? if($balanceAmount>0){?>Advance Balance = <?=$balanceAmount?><? }?></td>
	  </tr>
		<tr>
			<td height="30" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?
			if( $editMode || $addMode )
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
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
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierPayments.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSupplierPayments(document.frmSupplierpayments);">												</td>
												
												<?} else{?>

												<td align="center" colspan="2">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierPayments.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddSupplierPayments(document.frmSupplierpayments);">												</td>
											
												<?} ?>
											</tr>
		<input type="hidden" name="hidSupplierPaymentsId" value="<?=$supplierPaymentsId;?>">
		<tr><TD height="5"></TD></tr>
		<tr><TD colspan="2" align="center" style="padding-left:10px; padding-right:10px;">
			<table>
				<TR>
				<TD valign="top">
				<fieldset>
				<table>
					 <tr>
						<td class="fieldName" nowrap="true">Date of Payment:</td>
						<td>
							<input name="paymentDate" type="text" id="paymentDate" size="9" value="<? if($editMode==true) { echo $enteredDate; } else { echo date("d/m/Y");}?>" autocomplete="off" onchange="xajax_displayOtherEntry(document.getElementById('paymentDate').value, document.getElementById('supplier').value, '<?=$supplierPaymentsId?>'); xajax_displayAdvanceEntry(document.getElementById('paymentDate').value, document.getElementById('supplier').value, '<?=$supplierPaymentsId?>',document.getElementById('fromDate').value, document.getElementById('toDate').value, document.getElementById('selSettlementDate').value);" />
						</td>
					</tr>
					<tr>
					    <td class="fieldName" nowrap="true">*Cheque/DD:</td>
					    <td>
						<select name="paymentMethod" id="paymentMethod" onchange="showPaymentMethod();">
							<option value="">--Select--</option>
							<option value="CH" <?=($paymentMethod=='CH')?"selected":""?>>CHEQUE</option>
							<option value="DD" <?=($paymentMethod=='DD')?"selected":""?>>DD</option>
						</select>
					    </td>
					</tr>
					<TR>
					    <td class="fieldName" nowrap="true">*<span id="pMthodId"></span> No:</td>
					    <td>
						<input name="chequeNo" type="text" id="chequeNo" size="20" value="<?=$cheque?>">
						</td>
					</TR>
					<tr>
					    <td class="fieldName" nowrap="true">*Issuing Bank:</td>
					    <td>
						<input type="text" name="bankName" id="bankName" size="24" value="<?=$bankName?>">
					    </td>
					</tr>
					<TR>
					    <td class="fieldName" nowrap="true">Payable At:</td>
					    <td>
						<input type="text" name="payableAt" id="payableAt" size="24" value="<?=$payableAt?>">
					    </td>
					</TR>
					<tr>
					    <td class="fieldName">*Amount (Rs.):</td>
					    <td>
						<input name="amount" type="text" id="amount" size="12" value="<?=$paidAmount?>" style="text-align:right;">
						</td>
					</tr>	
					<tr>
					    <td class="fieldName" nowrap="true">Reason for Payment:</td>
					    <td>						
						<textarea name="paymentReason" id="paymentReason" rows="3"><?=$paymentReason?></textarea>
					    </td>
					</tr>
					<tr>
					    <td class="fieldName" nowrap="true">*Accounts Ref No. :</td>
					    <td>
						<input type="text" name="accountEntryNo" id="accountEntryNo" size="24" value="<?=$accountEntryNo?>">
					    </td>
					</tr>					
				</table>
				</fieldset>
				</TD>
				<TD>&nbsp;</TD>
				<TD valign="top">
				<fieldset>
				<table>
					<tr>
					 <td class="fieldName">*Supplier:</td>
				   	 <td>
                                                <select name="supplier" id="supplier" onchange=" xajax_displayOtherEntry(document.getElementById('paymentDate').value, document.getElementById('supplier').value, '<?=$supplierPaymentsId?>'); xajax_displaySetldRecs(document.getElementById('supplier').value, document.getElementById('paymentType').value, document.getElementById('fromDate').value, document.getElementById('toDate').value, document.getElementById('selSettlementDate').value); xajax_getSetldDates(document.getElementById('supplier').value, document.getElementById('fromDate').value, document.getElementById('toDate').value, ''); xajax_displayAdvanceEntry(document.getElementById('paymentDate').value, document.getElementById('supplier').value, '<?=$supplierPaymentsId?>', document.getElementById('fromDate').value, document.getElementById('toDate').value, document.getElementById('selSettlementDate').value);">
                                                  <option value="">--select--</option>
                                                  <?php
							foreach ($supplierRecords as $fr) {
								$supplierId	=	$fr[0];
								$supplierName	=	stripSlash($fr[2]);
								$selected	=	"";
								if ($supplierId==$paidSupplierId){
									$selected	=	"selected";
								}					
						?>
                              <option value="<?=$supplierId?>" <?=$selected?>><?=$supplierName?>
                                                  </option>
                                                  <? } ?>
                                                </select></td>
					  </tr>	
					<tr>
					    <td class="fieldName" nowrap="true">*Payment Type:</td>
					    <td>
						<select name="paymentType" id="paymentType" onchange="showPaymentType(); ">
							<option value="">--Select--</option>
							<option value="A" <?=($paymentType=='A')?"selected":""?>>ADVANCE</option>
							<option value="S" <?=($paymentType=='S')?"selected":""?>>SETTLEMENT</option>
						</select>
					    </td>
					</tr>
		<tr id="setldDateRow">
			<td colspan="2">
				<table>
				<TR><TD colspan="2">
				<table width="100%">
							<tr>
							<TD class="fieldName">From</TD>
							<td>
	<input type="text" name="fromDate" id="fromDate" size="9" value="<?=$sFromDate?>" onchange="xajax_displaySetldRecs(document.getElementById('supplier').value, document.getElementById('paymentType').value, document.getElementById('fromDate').value, document.getElementById('toDate').value, document.getElementById('selSettlementDate').value); xajax_getSetldDates(document.getElementById('supplier').value, document.getElementById('fromDate').value, document.getElementById('toDate').value, ''); xajax_displayAdvanceEntry(document.getElementById('paymentDate').value, document.getElementById('supplier').value, '<?=$supplierPaymentsId?>', document.getElementById('fromDate').value, document.getElementById('toDate').value, document.getElementById('selSettlementDate').value);">
							</td>
							<TD class="fieldName">To</TD>
							<td>
	<input type="text" name="toDate" id="toDate" size="9" value="<?=$sToDate?>" onchange="xajax_displaySetldRecs(document.getElementById('supplier').value, document.getElementById('paymentType').value, document.getElementById('fromDate').value, document.getElementById('toDate').value, document.getElementById('selSettlementDate').value); xajax_getSetldDates(document.getElementById('supplier').value, document.getElementById('fromDate').value, document.getElementById('toDate').value, ''); xajax_displayAdvanceEntry(document.getElementById('paymentDate').value, document.getElementById('supplier').value, '<?=$supplierPaymentsId?>', document.getElementById('fromDate').value, document.getElementById('toDate').value, document.getElementById('selSettlementDate').value);">
							</td>
							</TR>
						</table>
				</TD></TR>
				<tr>
					<TD class="fieldName">*Settlement Date :</TD>
					<td>
						<select name="selSettlementDate" id="selSettlementDate" onchange="xajax_displaySetldRecs(document.getElementById('supplier').value, document.getElementById('paymentType').value, document.getElementById('fromDate').value, document.getElementById('toDate').value, document.getElementById('selSettlementDate').value); xajax_displayAdvanceEntry(document.getElementById('paymentDate').value, document.getElementById('supplier').value, '<?=$supplierPaymentsId?>', document.getElementById('fromDate').value, document.getElementById('toDate').value, document.getElementById('selSettlementDate').value);">
							<option value="">--Select--</option>
						</select>
					</td>
				</tr>
				</table>
			</td>
		</tr>								
				</table>
				</fieldset>
				</TD>
				</TR>
			</table>
		</TD></tr>	
<!--  Settlement Row Starts here-->
		<tr id="setlmentRow">
		<TD colspan="2" valign="top" align="center">
			<table>
			<TR>
			<TD>
			<fieldset>
			<legend class="listing-item">Settlement</legend>
			<table>
				<tr><TD id="setldListRow"></TD></tr>
				<!--<TR>
				<TD>
				<table>
				<TR>
					<TD class="fieldName">*Based on </TD>
					<td>
						<select name="dateType" id="dateType" onchange=" xajax_setldChallanRecs(document.getElementById('supplier').value, document.getElementById('dateType').value, document.getElementById('fromDate').value, document.getElementById('toDate').value, document.getElementById('paymentType').value, '<?=$selChallan?>', '<?=$billingCompany?>', '', '');">
							<option value="">--Select--</option>
							<option value="WCD" <?=($dateType=='WCD')?"selected":""?>>Wt Challan Date</option>
							<option value="SCD" <?=($dateType=='SCD')?"selected":""?>>Supplier Date</option>
						</select>
					</td>					
				</TR>				
         			<tr>
					<TD class="fieldName">*Challan Nos :</TD>
					<td>
						<select multiple="true" name="selChallan[]" id="selChallan" size="10" style="width:120px;">
							<option value="">--Select--</option>
						</select>
					</td>
				</tr>
				<tr>
				<TD class="fieldName" nowrap="true">*Payment from Company:</TD>
				<td>
					<select name="billingCompany" id="billingCompany">		
						<option value="">--Select--</option>				
					</select>
				</td>
			</tr>
				</table>
				</TD>
				</TR>-->		
			</table>
			</fieldset>
			</TD>
			</TR>
			</table>	
		</TD>
		</tr>		
<!-- Display Any Entry Exist for the Selected Date of Payment -->
	<tr><TD id="supplierOtherEntry" colspan="2" valign="top" align="center"></TD></tr>
<!-- Display Any AdvanceEntry Exist for the Selected Date of Payment -->
<tr><TD id="supplierAdvanceEntry" colspan="2" valign="top" align="center"></TD></tr>
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierPayments.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSupplierPayments(document.frmSupplierpayments);">												</td>
												
												<?} else{?>

												<td align="center" colspan="2">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierPayments.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddSupplierPayments(document.frmSupplierpayments);">												</td>
<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<tr>
			<td height="10" ></td>
		</tr>
		<?
			}
			
			# Listing Fish-Grade Starts
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
								<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Supplier Payments </td>
								<td background="images/heading_bg.gif" nowrap="true" align="right">
									<table cellpadding="0" cellspacing="0">
									  <tr>
					<td nowrap="nowrap">
					<table cellpadding="0" cellspacing="0">
                      			<tr>
					  	<td class="listing-item">&nbsp;From:&nbsp;</td>
                                    		<td nowrap="nowrap"> 
                            		<?php 
						if ($dateFrom=="") $dateFrom=date("d/m/Y");
					?>
                            			<input type="text" id="selectFrom" name="selectFrom" size="9" value="<?=$dateFrom?>">
						</td>
					    <td class="listing-item">&nbsp;</td>
				            <td class="listing-item">&nbsp;Till:&nbsp;</td>
                                    <td> 
                                      <?php 
					   if($dateTill=="") $dateTill=date("d/m/Y");
				      ?>
                                      		<input type="text" id="selectTill" name="selectTill" size="9"  value="<?=$dateTill?>">
					</td>
						<td class="listing-item">&nbsp;Supplier:&nbsp;</td>			
						<td>
							<select name="supplierFilter" id="supplierFilter" style="width:100px;">
							<option value="">-- Select All --</option>
							<?php
								foreach($supplierRecords as $fr)
								{						
									$fSupplierId	= $fr[0];
									$fSupplierName	= stripSlash($fr[2]);
									$selected	= "";
									if ($fSupplierId == $supplierFilterId){
										$selected = "selected";
									}
							?>
							<option value="<?=$fSupplierId?>" <?=$selected?>><?=$fSupplierName?></option>
							<?php
								 } 
							?>
							</select>
						</td>		
					   <td class="listing-item">&nbsp;</td>
					        <td>
							<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search">
						</td>
                            <td class="listing-item" nowrap >&nbsp;</td>
                          </tr>
                    </table>
				</td>
			</tr>
			</table>
			</td>
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
							</tr>
							<tr>	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierPaymentsRecordsSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?>
		<input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintSupplierPayments.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>&supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?></td>
										</tr>
									</table>								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
							<?
								if($errDel!="")
								{
							?>
							<tr>
								<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
							</tr>
							<?
								}
							?>
							<tr>
								<td width="1" ></td>
								<td colspan="2" style="padding-left:5px;padding-right:5px;" >
		<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?php
			if (sizeof($supplierPaymentsRecords) > 0) {
				$i	=	0;
		?>
		 <? if($maxpage>1){?>
			<tr bgcolor="#FFFFFF">
			<td colspan="11" style="padding-right:10px">
			<div align="right">
			<?php 				 			  
			$nav  = '';
			for($page=1; $page<=$maxpage; $page++) {
				if ($page==$pageNo) {
					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
  				} else {
				      	$nav.= " <a href=\"SupplierPayments.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&supplierFilter=$supplierFilterId\" class=\"link1\">$page</a> ";
	   			}
			}
			if ($pageNo > 1) {
		   		$page  = $pageNo - 1;
   				$prev  = " <a href=\"SupplierPayments.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&supplierFilter=$supplierFilterId\"  class=\"link1\"><<</a> ";
	 		} else {
   				$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
			}
			if ($pageNo < $maxpage)	{
		   		$page = $pageNo + 1;
   				$next = " <a href=\"SupplierPayments.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&supplierFilter=$supplierFilterId\"  class=\"link1\">>></a> ";
	 		} else {
   				$next = '&nbsp;'; // we're on the last page, don't print next link
   				$last = '&nbsp;'; // nor the last page link
			}
			// print the navigation link
			$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
			echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr><? }?>
	<tr  bgcolor="#f2f2f2"  align="center">
		<td width="20" height="1"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
		<td nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Date</td>
		<? if (!$supplierFilterId) {?>
		<td nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier</td>
		<? }?>
		<td nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Payment<br>Method</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Cheque/DD<br> No</td>
		<td nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Issuing Bank</td>	
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Payable At</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Payment<br>Type</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Accounts<br>Ref No</td>	
		<td align="right" class="listing-head" style="padding-left:5px; padding-right:5px;">Amount</td>
		<? if($edit==true){?>
			<td class="listing-head" width="65"></td>
		<? }?>
	</tr>
	<?php
		$totalAmtPaid = 0;
		foreach ($supplierPaymentsRecords as $spr) {						
			$i++;
			$paymentId	= $spr[0];			
			$sEnteredDate	= dateFormat($spr[4]);
			$chequeNo	= $spr[2];
			$amountPaid	= $spr[3];
			$totalAmtPaid	+= $amountPaid;
			$supplierName	= $spr[5];
			$selBankName	= $spr[6];
			$sPayableAt	= $spr[7];
			$spPmtMethod	= ($spr[8]=='CH')?"CHEQUE":"DD";
			$spPmtType	= ($spr[9]=='A')?"ADVANCE":"SETTLEMENT";
			$spACEntryNo	= $spr[10];
	?>
	<tr  bgcolor="WHITE"  >
		<td width="20" height="25" class="listing-item"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$paymentId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$sEnteredDate?></td>
		<? if (!$supplierFilterId) {?>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$supplierName;?></td>
		<? }?>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$spPmtMethod;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$chequeNo;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selBankName;?></td>				
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$sPayableAt?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center"><?=$spPmtType?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$spACEntryNo?></td>
		<td class="listing-item"  align="right" style="padding-left:5px; padding-right:5px;"><?=$amountPaid?></td>
		<? if($edit==true){?>
			<td class="listing-item" width="65" align="center" style="padding-left:5px; padding-right:5px;"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$paymentId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='SupplierPayments.php';"  ></td>
		<? }?>
	</tr>
	<?php
		}
		$colSpan = (!$supplierFilterId)?9:8;
	?>
	<tr bgcolor="WHITE">
		<TD colspan="<?=$colSpan?>" class="listing-head" align="right">Total:&nbsp;</TD>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalAmtPaid,2,'.',',')?></strong></td>
		<td class="listing-item">&nbsp;</td>
	</tr>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	 <? if($maxpage>1){?>
	<tr bgcolor="#FFFFFF"><td colspan="11" style="padding-right:10px"><div align="right">
	<?php 				 			  
	 $nav  = '';
	for($page=1; $page<=$maxpage; $page++) {
		if ($page==$pageNo) {
			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page
		} else {
			$nav.= " <a href=\"SupplierPayments.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&supplierFilter=$supplierFilterId\" class=\"link1\">$page</a> ";
		}
	}
	if ($pageNo > 1) {
  		$page  = $pageNo - 1;
   		$prev  = " <a href=\"SupplierPayments.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&supplierFilter=$supplierFilterId\"  class=\"link1\"><<</a> ";
 	} else {
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
	}

	if ($pageNo < $maxpage) {
   		$page = $pageNo + 1;
   		$next = " <a href=\"SupplierPayments.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&supplierFilter=$supplierFilterId\"  class=\"link1\">>></a> ";
	 } else {
   		$next = '&nbsp;'; // we're on the last page, don't print next link
   		$last = '&nbsp;'; // nor the last page link
	}
	// print the navigation link
	$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
	echo $first . $prev . $nav . $next . $last . $summary; 
	 ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr><? }?>
										<?
											}
											else
											{
										?>
										
										<tr bgcolor="white">
											<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
										</tr>	
										<?
											}
										?>
									</table>
	<input type="hidden" name="paidSupplier" value="<?=$paidSupplierId?>">				
		</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
							<tr >	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierPaymentsRecordsSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?>
		<input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintSupplierPayments.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>&supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?></td>
										</tr>
									</table>			
					</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
						</table>	
				</td>
				</tr>
			</table>
		<!-- Form fields end   -->		
		</td>
	</tr>
	<tr>
		<td height="10">
			<input type="hidden" name="hidSupplierFilterId" value="<?=$supplierFilterId?>">
		</td>
	</tr>	
	</table>
	<?php
		if ($addMode || $editMode) {
	?>
	<script language="JavaScript" type="text/javascript">
		showPaymentType();
		showPaymentMethod()
	</script>
	<?php
		}
	?>
	<?php
		if ($editMode || $supplierAmtPaid) {
	?>
	<script language="JavaScript" type="text/javascript">
		/*xajax_setldChallanRecs('<?=$paidSupplierId?>', '<?=$dateType?>', '<?=$sFromDate?>', '<?=$sToDate?>', '<?=$paymentType?>', '<?=$selChallan?>', '<?=$billingCompany?>', '<?=$mode?>', '<?=$selSettlementDate?>');*/
		// Get Setld Date
		xajax_getSetldDates('<?=$paidSupplierId?>', '<?=$sFromDate?>', '<?=$sToDate?>', '<?=$selSettlementDate?>');
		// Display Setld Recs
		xajax_displaySetldRecs('<?=$paidSupplierId?>', '<?=$paymentType?>', '<?=$sFromDate?>', '<?=$sToDate?>', '<?=$selSettlementDate?>');
		<? if ($enteredDate && $paidSupplierId) {?>
			xajax_displayOtherEntry('<?=$enteredDate?>', '<?=$paidSupplierId?>', '<?=$supplierPaymentsId?>');
			xajax_displayAdvanceEntry('<?=$enteredDate?>', '<?=$paidSupplierId?>', '<?=$supplierPaymentsId?>', '<?=$sFromDate?>', '<?=$sToDate?>', '<?=$selSettlementDate?>');
		<? }?>
	</script>
	<?php
		}
	?>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "fromDate",         // ID of the input field
			eventName  : "click",	    // name of event
			button : "fromDate", 
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
			inputField  : "toDate",         // ID of the input field
			eventName  : "click",	    // name of event
			button : "toDate", 
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
			inputField  : "paymentDate",         // ID of the input field
			eventName  : "click",	    // name of event
			button : "paymentDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectTill", 
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
