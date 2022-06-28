<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$checked		=	"";
	
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
	//----------------------------------------------------------
	
	# Update
	if ($p["cmdTransporterPayment"]!="") {	
		$selTransporter		=	$p["transporter"];
		$rowCount		=	$p["hidRowCount"];
		$totalPayingAmount 	= 	$p["totalpaidAmount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$billNo	=	$p["billNo_".$i];
			$reEdited	= 	$p["reEdit_".$i];
			/*if ($reEdited=="" && ($isAdmin==true || $reEdit==true || $companySpecific==true)) {	*/
			if ($reEdited=="" || $isAdmin==true || $reEdit==true || $companySpecific==true) {
				$paid	=	($p["paid_".$i]=="")?N:$p["paid_".$i];
			} else {
				$paid = "";
			}
			if ($billNo!="" && $paid!="") {
				$updateSupplierPayment = $transporterSettlementSummaryObj->updateTransporterBillRecords($selTransporter, $billNo,$paid);
			}
		}
		if ($updateSupplierPayment!="" && ($totalPayingAmount!="" && $totalPayingAmount!=0)) {
			header("Location:TransporterPayments.php?transporter=$selTransporter&totalPayingAmount=$totalPayingAmount");
		}		
	}
	
	if ($p["transporter"]=="") $selTransporter = $g["transporter"];
	else 			$selTransporter = $p["transporter"];
	
	# select record between selected date
	if ($p["supplyFrom"]=="" && $p["supplyTill"]=="") {
		$dateFrom = $g["supplyFrom"];
		$dateTill = $g["supplyTill"];
	} else {
		$dateFrom = $p["supplyFrom"];
		$dateTill = $p["supplyTill"];
	}
	
	$date1		=	explode("/", $dateFrom);
	$fromDate	=	$date1[2]."-".$date1[1]."-".$date1[0];
	
	$date2		=	explode("/", $dateTill);
	$tillDate	=	$date2[2]."-".$date2[1]."-".$date2[0];
	
	
	#Select the records based on date
	if ($dateFrom!="" && $dateTill!="") {	
		
		# Get all Transporter	
		$transporterRecords	= $transporterSettlementSummaryObj->fetchTransporterRecords($fromDate, $tillDate);

		# Get Transporter Settlement Records
		$transporterSettlementRecords	= $transporterSettlementSummaryObj->filterTransporterSettlementRecords($selTransporter, $fromDate, $tillDate);
	}

	# Display heading
	if ($editMode)	$heading	= $label_editPurchaseSettlement;
	else		$heading	= $label_addTransporterSettlementSummary;	

	$ON_LOAD_PRINT_JS	= "libjs/TransporterSettlementSummary.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmTransporterSettlementSummary" action="TransporterSettlementSummary.php" method="Post">
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
                        <td colspan="2" height="10"></td>
                      </tr>
			<?
				if (sizeof($transporterSettlementRecords)>0) {
			?>
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center">
				<? if($isAdmin==true || $reEdit==true || $companySpecific==true || $edit==true){?>
					<input name="cmdTransporterPayment" type="submit" class="button" id="cmdTransporterPayment" onClick="return updateTransporterPayment(document.frmTransporterSettlementSummary);" value=" Save ">
				<? }?>
			</td>
                        <?} ?>
                      </tr>
			<? } else {?>
				 <tr> 
                        <td colspan="2" height="20"></td>
                      </tr>
			<?}?>
                      <input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
                      <tr>
                        <td colspan="3" nowrap height="5"></td>
                        </tr>
			<tr>
                        <td class="fieldName" nowrap >&nbsp;</td>
                        <td colspan="2" align="center">
			<table width="250">
                                  <tr> 
                                    <td class="fieldName"> From:</td>
                                    <td> 
                                      <input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" autocomplete="off" onchange="return getTransporter();">
				</td>
				<td class="fieldName">To:</td>
				<td>
                                        <input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" autocomplete="off" onChange="return getTransporter();">
					</td>
					 <td class="fieldName">Transporter:</td>
                                    <td>
                                      <select name="transporter" id="transporter">
                                        <option value="">-- Select --</option>
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
	<td>
		<input name="cmdSearch" type="submit" id="cmdSearch" value=" Search" class="button" onclick="return validateTransporterSettlementSummary(document.frmTransporterSettlementSummary);">
	</td>
                                  </tr>
                                </table>
			</td>
                        </tr>
                  <? 
		     if (sizeof($transporterSettlementRecords)>0) {
			  $i = 0;
		  ?>
         <tr>
              <td colspan="4" align="center" style="padding-left:10px; padding-right:10px;">
		<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
              	<tr bgcolor="#f2f2f2" align="center"> 
                <th nowrap="nowrap" class="listing-head" style="padding-left:5px; padding-right:5px;" width="20%">Bill No </th>
                <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;" width="20%">Settled Date</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="20%">Total Amount</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="20%">Paid</th>
              </tr>
              <?php
		$totalAmt		= "";
		$grandTotalAmt		= 0;
		
		foreach($transporterSettlementRecords as $tsr){
			$i++;			
			$billNo		= $tsr[0];
			$settledDate	= dateFormat($tsr[1]);
			$totalAmt	= $tsr[2];
			$billPaid	= $tsr[3];

			$grandTotalAmt  += $totalAmt;
			$checked = "";
			if ($billPaid=='Y') {
				$checked = "Checked";
				$paidAmount += $totalAmt;
			} else {
				$unpaidAmount += $totalAmt;
			}
			$disabled = "";
			$edited	  = "";
			if ($billPaid=='Y' && $isAdmin==false && $reEdit==false && !$companySpecific) {
				$disabled = "readonly";
				$edited	  = 1;
			}		
	?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap height='25' style="padding-left:5px; padding-right:5px;" align="center">
			<?=$billNo?>
			<input type="hidden" name="billNo_<?=$i?>" value="<?=$billNo?>">
		</td>
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$settledDate?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><? echo number_format($totalAmt,2);?></td>
		<td align="center">
			<input name="paid_<?=$i;?>" type="checkbox" id="paid_<?=$i;?>" value="Y"  <?=$checked?> class="chkBox" onclick="paidAmount()">
			<input type="hidden" name="payingAmount_<?=$i;?>" id="payingAmount_<?=$i;?>" value="<?=$totalAmt?>">
			<input type="hidden" name="alreadyPaid_<?=$i;?>" id="alreadyPaid_<?=$i;?>" value="<? if($checked) echo 'Y';?>">
			<input type="hidden" name="reEdit_<?=$i;?>" value="<?=$edited?>"></td>
              </tr>
		<? 

		$prevSelBillCompanyId = $selBillCompanyId;
		}
		?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap>&nbsp;</td>
                <td class="listing-head" align="right" style="padding-left:5px; padding-right:5px;">TOTAL:</td>
		<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($grandTotalAmt,2);?></strong></td>
		<td>&nbsp;</td>
              </tr>
			  
      </table></td><input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>" >
                        </tr>
						<? }?>
                      <tr> 
                        <td colspan="4" align="center" class="err1"><? if(sizeof($transporterSettlementRecords)<=0 && $selTransporter!=""){ echo $msgNoSettlement;}?></td>
                        </tr>
			<? 
			  if(sizeof($transporterSettlementRecords)>0){
			 ?>
                      <tr>
                        <td colspan="4" align="center"><table>
  <tr>
    <td class="fieldName"> Paid:</td>
  <td class="listing-item"><strong><? echo number_format($paidAmount,2);?></strong>&nbsp;&nbsp;</td>
  <td class="fieldName">Unpaid: </td>
  <td class="listing-item"><strong><? echo number_format($unpaidAmount,2);?></strong></td>
  </tr>
  </table><input type="hidden" name="totalpaidAmount" id="totalpaidAmount"></td>
                      </tr>
					  <? }?>
			<tr>
                        <td colspan="3" nowrap height="5"></td>
                        </tr>
			<?
				if (sizeof($transporterSettlementRecords)>0) {
			?>
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center">
				<? if($isAdmin==true || $reEdit==true || $companySpecific==true || $edit==true){?>
					<input name="cmdTransporterPayment" type="submit" class="button" id="cmdTransporterPayment" onClick="return updateTransporterPayment(document.frmTransporterSettlementSummary);" value=" Save ">
				<? }?>
			</td>
                        <input type="hidden" name="cmdAddNew" value="1">
                        <?}?>
                      </tr>
			<? } else {?>
			<tr>
                        <td colspan="3" nowrap height="25"></td>
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
