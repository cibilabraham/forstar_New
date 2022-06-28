<?php
	require("include/include.php");
	require_once("lib/settlementhistory_ajax.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	true;
	$checked		=	"";

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
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	#Checking Confirm enabled or Disabled
	$acConfirmed = $manageconfirmObj->isACConfirmEnabled();
		
	$selectSupplier	= $p["supplier"];
	# select record between selected date
	$dateFrom = $p["supplyFrom"];
	$dateTill = $p["supplyTill"];
	$selSettlementDate = $p["selSettlementDate"];
	$billingCompany	 = $p["billingCompany"];
		
	$Date1		=	explode("/",$dateFrom);
	$fromDate	=	$Date1[2]."-".$Date1[1]."-".$Date1[0];
	
	$Date2		=	explode("/",$dateTill);
	$tillDate	=	$Date2[2]."-".$Date2[1]."-".$Date2[0];
	
	if ($dateFrom!="" && $dateTill!="") {
		#Filter Supplier for a date Range
		$supplierRecords = $settlementHistoryObj->fetchSupplierRecords($fromDate, $tillDate, $acConfirmed);

		# Get Billing Comapany  Records
		$billingCompanyRecords = $settlementHistoryObj->fetchBillingCompanyRecords($fromDate, $tillDate, $selectSupplier);	

		if ($selectSupplier!="") {
			#for selecting Payment  Date
			$paymentDateRecords = $settlementHistoryObj->getAllPaymentDate($fromDate, $tillDate, $selectSupplier, $billingCompany);		
		}	
	}
	
	if ($p["cmdSearch"]!="" && $dateFrom!="" && $dateTill!="" ) {
		#Select the records based on date		
		$purchaseStatementRecords = $settlementHistoryObj->filterPurchaseStatementRecords($selectSupplier, $fromDate, $tillDate, $acConfirmed, $selSettlementDate, $billingCompany);
	
	}


	# Display heading
	if ($editMode) $heading = $label_editPurchaseSettlement;
	else $heading = $label_addPurchaseSettlement;
	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSettlementHistory" action="SettlementHistory.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="98%">
		<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="60%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
						<tr>
						<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
						<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Settlement History</td>
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
                        <td colspan="4" align="center"><? if($print==true){?><!--input type="button" name="View" value=" View / Print" class="button" onClick="return printWindow('PrintSettlementHistory.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&supplier=<?=$selectSupplier?>',700,600);" <? if( sizeof($purchaseStatementRecords)==0) echo $disabled="disabled";?>--><? }?></td>
                        <?} ?>
                      </tr>
                      <input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
                      <tr>
                        <td colspan="3" nowrap height="5"></td>
                        </tr>
                      <tr>
                        <td class="fieldName" nowrap >&nbsp;</td>
                        <td align="center"><table width="250">
                                  <tr> 
                                    <td class="fieldName" nowrap="true"> From</td>
                                    <td> 
                                      <? $dateFrom = $p["supplyFrom"];?>
                                      <input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" autocomplete="off" onchange="submitForm('supplyFrom', 'supplyTill',frmSettlementHistory);"></td>
									  <td class="fieldName">To</td>
								    <td><? $dateTill = $p["supplyTill"];?>
                                        <input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" autocomplete="off" onChange="submitForm('supplyFrom', 'supplyTill',frmSettlementHistory);"></td>
                                  </tr>
                                </table></td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr> 
                        <td class="fieldName" nowrap >&nbsp;</td>
                        <td align="center">
			<table width="250" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td class="fieldName"></td>
                                    <td></td>
                                  </tr>
                                  <tr> 
                                    <td class="fieldName">Supplier:</td>
                                    <td> 
                                      <? $selectSupplier = $p["supplier"];?>
                                     <!-- <select name="supplier" onchange="this.form.submit();">-->
									  <!--<select id="supplier" name="supplier" onchange="xajax_getbillCompany('','',document.getElementById('supplier').value);" >-->
									 <select name="supplier" onchange="getSupplier(this)">
                                        <option value="">-- Select All--</option>
                                        <?
					foreach ($supplierRecords as $fr) {
							$supplierId	=	$fr[0];
							$supplierName	=	stripSlash($fr[2]);
							$selected	=	"";
							if ($supplierId == $selectSupplier) {
								$selected	=	"selected";
							}
					?>
                                        <option value="<?=$supplierId?>" <?=$selected?>> 
                                        <?=$supplierName?>
                                        </option>
                                        <? } ?>
                                      </select></td>
                                  </tr>
				<tr>
				<TD class="fieldName" nowrap="true">Billing Company:</TD>
				<td>
					<!--<select name="billingCompany" onchange="this.form.submit();" id="billingCompany">-->	
					<select name="billingCompany" onchange="getPaymentDate(this)" id="billingCompany">
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
				<? if ($selectSupplier) {?>
			<tr>
				<td class="fieldName" nowrap>Payment Date&nbsp;</td>
			        <td>
				  <select name="selSettlementDate">
				  <option value="">-- Select All --</option>
				  <? 
				  foreach ($paymentDateRecords as $sdr) {
					$settledDate	=	$sdr[0];
					$recordDate	=	$sdr[1];
					$selected = "";
					if ($settledDate==$selSettlementDate) $selected = "selected";
					if ($settledDate!=0000-00-00) {
				 ?>
				<option value="<?=$settledDate?>" <?=$selected;?> ><?=$recordDate;?> </option>
				<?
					}
				}
				?>
				</select></td></tr>
				<? }?>
			<? if ($dateFrom!="" && $dateTill!="") {?>
			<tr><TD height="5"></TD></tr>
			<tr>
				<TD></TD>
				<td><input type="submit" name="cmdSearch" value=" Search" class="button" ></td>
			</tr>
			<? }?>
                        </table></td>
                        <td>&nbsp;</td>
                      </tr>
<tr><TD height="10"></TD></tr>
<!-- Listing starts Here  -->
	<?php if(sizeof($purchaseStatementRecords)>0) {?>
	<tr><TD colspan="3">
		<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
              <tr bgcolor="#f2f2f2" align="center"> 
                <th nowrap="nowrap" class="listing-head" style="padding-left:5px; padding-right:5px;" width="20%">Challan No </th>
                <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;" width="20%">Date</th>
                <th class="listing-head" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" width="20%">Cost of Raw Material </th>
                <th class="listing-head" align="center" style="line-height:normal" width="20%">Transportation/<br />Ice/<br /> Commission if any </th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;" width="20%">Total</th>
              </tr>
              <?			
		$totalRMCost		= "";
		$totalRmSupplyCost	= "";
		$grandTotalRMCost	= "";
		$i = 0;		
		$prevSelBillCompanyId = "";
		$prevBillCompanyId = 0;
		$costRMArr = array();
		$rmSupplyCostArr = array();
		$grandTotalRMCostArr = array();
		$prevBillingArr = array();
		$p = 0;
		foreach($purchaseStatementRecords as $psr)
		{
			$i++;
			$challanId 		= $psr[0];
			$challanNo		= $psr[1];
			$selBillCompanyId 	= $psr[5];
			// Find Supply Cost
			$rmSupplyCost		= $settlementHistoryObj->getSupplyCost($challanNo, $selBillCompanyId);
			
			$enteredDate		= dateFormat($psr[2]);
				
			$totalRmSupplyCost	+=	$rmSupplyCost;
		
			$costRawMaterial	=	$psr[4];
			$totalCostOfRawMaterial = 	$costRawMaterial + $rmSupplyCost;
			$totalRMCost		+=	$costRawMaterial;
			$grandTotalRMCost 	=  $totalRMCost + $totalRmSupplyCost;

			
			if ($i==1) $prevBillCompanyId = $selBillCompanyId;	
			$costRMArr[$selBillCompanyId] 		+= $costRawMaterial;
			$rmSupplyCostArr[$selBillCompanyId]   	+= $rmSupplyCost;
			$grandTotalRMCostArr[$selBillCompanyId] += $costRawMaterial+$rmSupplyCost;
	
			if ($prevBillCompanyId!=$selBillCompanyId ) {
				$prevBillingArr[$p] = $prevSelBillCompanyId;
				echo '<tr bgcolor="#FFFFFF"> 
					<td class="listing-item" nowrap>&nbsp;</td>
					<td class="listing-head" align="center">TOTAL</td>
					<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong>'.number_format($costRMArr[$prevBillCompanyId],2).'</strong></td>
					<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong>'.number_format($rmSupplyCostArr[$prevBillCompanyId],2).'</strong></td>
					<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong>'.number_format($grandTotalRMCostArr[$prevBillCompanyId],2).'</strong></td>
					</tr>';
					$p++;
			}
			
			if ($prevSelBillCompanyId!=$selBillCompanyId) {				
				
				if ($selBillCompanyId>0) {	// Getting Rec from other billing company
					list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $billingCompanyObj->getBillingCompanyRec($selBillCompanyId);
				} else {	// Getting Rec from Company Details Rec
					list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $companydetailsObj->getForstarCompanyDetails();
				}
				echo '<tr bgcolor="white"><td class="fieldname" colspan="10" style="padding-left:10px; padding-right:10px;" nowrap><b>'.$companyName.'</b></td></tr>';				
			}
			$dispalyChallanNum	= $psr[6];
	?>
		
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap height='25' style="padding-left:5px; padding-right:5px;"><?=$dispalyChallanNum;?></TD>
                <td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" align="center"><?=$enteredDate?></td>
                <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"> 
                  <?=$costRawMaterial?></td>
                <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"> 
                  <? echo number_format($rmSupplyCost,2,'.','');?></td>
                <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"> 
                  <? echo number_format($totalCostOfRawMaterial,2,'.','');?></td>
              </tr>
			<?php				
			if (sizeof($purchaseStatementRecords)==$i && sizeof($costRMArr)>1) {
			?>
		<tr bgcolor="#FFFFFF"> 
			<td class="listing-item" nowrap>&nbsp;</td>
			<td class="listing-head" align="center">TOTAL</td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($costRMArr[$selBillCompanyId],2);?></strong></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($rmSupplyCostArr[$selBillCompanyId],2);?></strong></td>
			<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
			<? echo number_format($grandTotalRMCostArr[$selBillCompanyId],2);?></strong></td>
		</tr>
		<?php
			} // Sub Total
		?>
		
	<?
			
			$prevBillCompanyId = $selBillCompanyId;
			$prevSelBillCompanyId = $selBillCompanyId;
	  	} // Rec Loop Ends Hee
	?>
		<?php
		/*
		if (sizeof($prevBillingArr)>0) {
			foreach ($prevBillingArr as $pbr=>$pbId) {				
				if ($pbId>0) {	// Getting Rec from other billing company
					list($cName, $adr, $plce, $pCode, $cntry, $tNo, $fNo) = $billingCompanyObj->getBillingCompanyRec($pbId);
				} else {	// Getting Rec from Company Details Rec
					list($cName, $adr, $plce, $pCode, $cntry, $tNo, $fNo) = $companydetailsObj->getForstarCompanyDetails();
				}*/
		?>
		<!--<tr bgcolor="#FFFFFF"> 
			<td class="listing-item" nowrap>&nbsp;</td>
			<td class="listing-head" align="center">
				TOTAL<br/>
				<span class="listing-item" style="line-height:normal;font-size:7px;">(<?=$cName?>)</span>
			</td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($costRMArr[$pbId],2);?></strong></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($rmSupplyCostArr[$pbId],2);?></strong></td>
			<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
			<? echo number_format($grandTotalRMCostArr[$pbId],2);?></strong></td>
		</tr>-->
		<?php
			//} // Sub Total
		//} // Arr >0
		?>
			<tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap>&nbsp;</td>
                <td class="listing-head" align="center">
			<? if (sizeof($prevBillingArr)>0) {?>GR.<? }?>TOTAL
		</td>
                <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalRMCost,2);?></strong></td>
                <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalRmSupplyCost,2);?></strong></td>
                <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($grandTotalRMCost,2);?></strong></td>
              </tr>
			  </table></TD></tr>
		<? } else if ($dateFrom!="" && $dateTill!="") {?>
                      <tr> 
                        <td colspan="2" align="center" class="err1"><?=$msgNoSettlement?></td>
                        <td align="center" colspan="2">&nbsp;</td>
                      </tr>
		<? }?>
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center"><? if($print==true){?><!--input type="button" name="View" value=" View / Print" class="button" onClick="return printWindow('PrintSettlementHistory.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&supplier=<?=$selectSupplier?>',700,600);" <? if( sizeof($purchaseStatementRecords)==0) echo $disabled="disabled";?>--><? }?></td>
                        <input type="hidden" name="cmdAddNew" value="1">
                        <?}?>
                      </tr>
                      <tr> 
                        <td colspan="2"  height="10"><input type="hidden" name="hidSelectSupplier" value="<?=$selectSupplier?>"></td>
                      </tr>
                    </table></td></tr></table></td>
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

			
	</table><SCRIPT LANGUAGE="JavaScript">
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
	</SCRIPT><SCRIPT LANGUAGE="JavaScript">
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

	function getSupplier(formObj)
	{
		showFnLoading(); 
		formObj.form.submit();

	}

	function getPaymentDate(formObj)
	{
	showFnLoading(); 
	formObj.form.submit();
	}
	</SCRIPT>
	
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
