<?php
	require("include/include.php");
	require("lib/SettlementSummary_ajax.php");
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
	
	if($g["billingCompany"]) 	$billingCompany	=	$g["billingCompany"];
	else 				$billingCompany	=	$p["billingCompany"];

	# Update
	if ($p["cmdSupplierPayment"]!="") {	
		$selectSupplier		= $p["supplier"];
		$rowCount		= $p["hidRowCount"];
		$totalPayingAmount 	= $p["totalpaidAmount"];

		$setldFrom 		= $p["supplyFrom"];
		$setldTill 		= $p["supplyTill"];

		$challanArr = array();
		$cnt = 0;
		for ($i=1; $i<=$rowCount; $i++) {
			$challanEntryId	=	$p["challanEntryId_".$i];
			$reEdited	= 	$p["reEdit_".$i];
			
			if ($reEdited=="" || $isAdmin==true || $reEdit==true || $companySpecific==true) {
			$paid	=	($p["paid_".$i]=="")?N:$p["paid_".$i];
			} else {
				$paid = "";
			}
			
			echo($paid);
			//echo("<br><br>");
			
			if ($challanEntryId!="" && $paid!="") {				
				$updateSupplierPayment = $settlementsummaryObj->updateChallanPayment($challanEntryId, $paid);
				if ($paid=='Y') {
					$challanArr[$cnt] = $challanEntryId;
					$cnt++;
				}
			}
		} // Loop Ends here
		
		
		
		if ($updateSupplierPayment!="" && ($totalPayingAmount!="" || $totalPayingAmount!=0)) {
			$selChallan = implode(",",$challanArr);
			header("Location:SupplierPayments.php?supplier=$selectSupplier&totalPayingAmount=$totalPayingAmount&setldFrom=$setldFrom&setldTill=$setldTill&setldBillingCompany=$billingCompany&paymentType=S&setldChallan=".base64_encode($selChallan));
		}		
	} // Supplier payment Ends here
	
	if ($p["supplier"]=="") $selectSupplier = $g["supplier"];
	else 			$selectSupplier = $p["supplier"];
	
	# select record between selected date
	if ($p["supplyFrom"]=="" && $p["supplyTill"]=="") {
		$dateFrom = $g["supplyFrom"];
		$dateTill = $g["supplyTill"];
	} else {
		$dateFrom = $p["supplyFrom"];
		$dateTill = $p["supplyTill"];
	}
		
	#Select the records based on date
	if ($dateFrom!="" && $dateTill!="") {	
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$supplierRecords	= $settlementsummaryObj->fetchSupplierRecords($fromDate, $tillDate);
		# Get Billing Comapany  Records
		$billingCompanyRecords = $settlementsummaryObj->fetchBillingCompanyRecords($fromDate, $tillDate, $selectSupplier);
		$settlementRecords	= $settlementsummaryObj->filterPurchaseStatementRecords($selectSupplier, $fromDate, $tillDate, $billingCompany);
	}

	# Display heading
	if ($editMode)	$heading	= $label_editPurchaseSettlement;
	else		$heading	= $label_addPurchaseSettlement;	
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav
	$ON_LOAD_PRINT_JS	= "libjs/settlementsummary.js";
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSettlementSummary" action="SettlementSummary.php" method="Post">
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
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center">
				<? if($isAdmin==true || $reEdit==true || $companySpecific==true || $edit==true){?>
					<input name="cmdSupplierPayment" type="submit" class="button" id="cmdSupplierPayment" onClick="return validateSettlementSummary(document.frmSettlementSummary);" value=" Save ">
				<? }?>
			</td>
                        <?} ?>
                      </tr>
                      <input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
                      <tr>
                        <td colspan="3" nowrap height="5"></td>
                        </tr>
	<tr>
              <td colspan="4" align="center" style="padding-left:10px; padding-right:10px;">
		<fieldset>
		<table>
			<TR>
				<TD valign="top">
					<table>
                                  <tr> 
                                    <td class="fieldName"> From</td>
                                    <td> 
            				<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" onchange="submitForm('supplyFrom','supplyTill',document.frmSettlementSummary);" autocomplete="off">
				</td>
				  <td class="fieldName">To</td>
				    <td>
                                        <input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" onChange="submitForm('supplyFrom','supplyTill',document.frmSettlementSummary);" autocomplete="off"></td>
                                  </tr>
                                </table>
				</TD>
				<TD>&nbsp;</TD>
				<td valign="top">
					<table>
						<tr> 
                                    <td class="fieldName">Supplier:</td>
                                    <td>
                                      <!--<select name="supplier" onchange="this.form.submit();">-->
<select name="supplier" onchange="functionLoad(this);">
                                        <option value="">-- Select --</option>
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
					</table>
				</td>
				<td>
					<table>
						<?php
					//if (sizeof($supplierRecords)>0) {
				?>
				<tr>
				<TD class="fieldName" nowrap="true">Billing Company:</TD>
				<td>
					<!--<select name="billingCompany" onchange="this.form.submit();">-->
					<select name="billingCompany" onchange="functionLoad(this);">
					<option value="">--Select--</option>
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
				<?php
					//}
				?>
					</table>
				</td>
			</TR>
		</table>
		</fieldset>
		</td>
         </tr>
	<tr><TD height="5"></TD></tr>
                      <!--<tr>
                        <td class="fieldName" nowrap >&nbsp;</td>
                        <td colspan="2" align="center">
				<table width="250">
                                  <tr> 
                                    <td class="fieldName"> From</td>
                                    <td> 
            				<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" onchange="submitForm('supplyFrom','supplyTill',document.frmSettlementSummary);" autocomplete="off">
				</td>
				  <td class="fieldName">To</td>
				    <td>
                                        <input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" onChange="submitForm('supplyFrom','supplyTill',document.frmSettlementSummary);" autocomplete="off"></td>
                                  </tr>
                                </table></td>
                        </tr>-->
                      <!--<tr> 
                        <td class="fieldName" nowrap >&nbsp;</td>
                        <td colspan="2" align="center"><table width="250" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td class="fieldName"></td>
                                    <td></td>
                                  </tr>
                                  <tr> 
                                    <td class="fieldName">Supplier:</td>
                                    <td>
                                      <select name="supplier" onchange="this.form.submit();">
                                        <option value="">-- Select --</option>
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
				<?php
					//if (sizeof($supplierRecords)>0) {
				?>
				<tr>
				<TD class="fieldName" nowrap="true">Billing Company:</TD>
				<td>
					<select name="billingCompany" onchange="this.form.submit();">		
					<option value="">--Select--</option>
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
				<?php
					//}
				?>
                        </table></td>
                 </tr>-->
                  <? 
		     if (sizeof($settlementRecords)>0) {
			  $i = 0;
		  ?>
         <tr>
              <td colspan="4" align="center" style="padding-left:10px; padding-right:10px;">
		<table width="80%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
              	<tr bgcolor="#f2f2f2" align="center"> 
                <th nowrap="nowrap" class="listing-head" style="padding-left:5px; padding-right:5px;" width="20%">Challan No </th>
                <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;" width="20%">Date</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;" width="20%">RM Cost</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="20%">RM Supply Cost</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="20%">Total Cost</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="20%">Paid</th>
              </tr>
              <?php
		$totalCost		= "";		
		$totalRmSupplyCost	= "";
		$totalRMCost		= "";
		$grandTotalCost		= "";
		$prevSelBillCompanyId 	= "";
		$prevBillCompanyId 	= 0;
		$costRMArr 	 = array();
		$rmSupplyCostArr = array();
		$grandTotalRMCostArr = array();
		$prevBillingArr = array();
		$p = 0;
		foreach($settlementRecords as $psr){
			$i++;
			$challanEntryId		=	$psr[0];
			$challanNo		=	$psr[1];
			$enteredDate		= 	dateFormat($psr[2]);	
			$selBillCompanyId 	=	$psr[7];
			$displayChallanNum	= 	$psr[8];
			if ($i==1) $prevBillCompanyId = $selBillCompanyId;
			# Check All RM Settled If SETTLED Return false else true
			$checkAllRMSettled = $settlementsummaryObj->challanRecords($fromDate, $tillDate, $challanEntryId, $selectSupplier);
			if ($prevSelBillCompanyId!=$selBillCompanyId && $checkAllRMSettled) {
				if ($selBillCompanyId>0) {	// Getting Rec from other billing company
					list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $billingCompanyObj->getBillingCompanyRec($selBillCompanyId);
				} else {	// Getting Rec from Company Details Rec
					list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $companydetailsObj->getForstarCompanyDetails();
				}
				echo '<tr bgcolor="white"><td class="fieldname" colspan="10" style="padding-left:10px; padding-right:10px;" nowrap><b>'.$companyName.'</b></td></tr>';
			}	
			
			if (!$checkAllRMSettled) {			
				// Find Supply Cost
				$rmSupplyCost		=	$purchasestatementObj->getSupplyCost($challanNo, $selBillCompanyId);
				$totalRmSupplyCost	+=	$rmSupplyCost;
	
				$costRawMaterial	=	$psr[4];
				$totalRMCost		+=	$costRawMaterial;
				$totalCost		=	$costRawMaterial + $rmSupplyCost;
				$challanPaid 		= 	$psr[5];
				$grandTotalCost 	=	$totalRMCost + $totalRmSupplyCost;
				$checked = "";
				if ($challanPaid=='Y') {
					$checked = "Checked";
					$paidAmount += $totalCost;
				} else {
					$unpaidAmount += $totalCost;
				}
				$disabled = "";
				$edited	  = "";
				if ($challanPaid=='Y' && $isAdmin==false && $reEdit==false && !$companySpecific) {
					$disabled = "readonly";
					$edited	  = 1;
				}	

				$costRMArr[$selBillCompanyId] 		+= $costRawMaterial;
				$rmSupplyCostArr[$selBillCompanyId]   	+= $rmSupplyCost;
				$grandTotalRMCostArr[$selBillCompanyId] += $costRawMaterial+$rmSupplyCost;
				//echo "$prevBillCompanyId!=$selBillCompanyId<br/>";	
				if ($prevBillCompanyId!=$selBillCompanyId ) {
					$prevBillingArr[$p] = $prevSelBillCompanyId;
					echo '<tr bgcolor="#FFFFFF"> 
						<td class="listing-item" nowrap>&nbsp;</td>
						<td class="listing-head" align="center">TOTAL</td>
						<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong>'.number_format($costRMArr[$prevBillCompanyId],2).'</strong></td>
						<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong>'.number_format($rmSupplyCostArr[$prevBillCompanyId],2).'</strong></td>
						<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong>'.number_format($grandTotalRMCostArr[$prevBillCompanyId],2).'</strong></td>
						<td></td>
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
	?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap height='25' style="padding-left:5px; padding-right:5px;">
			<?=$displayChallanNum?>
			<input type="hidden" name="challanEntryId_<?=$i?>" value="<?=$challanEntryId?>">
		</td>
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$enteredDate?></td>
                <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$costRawMaterial?><input type="hidden" name="payingAmount_<?=$i;?>" id="payingAmount_<?=$i;?>" value="<?=$totalCost?>"> </td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><? echo number_format($rmSupplyCost,2);?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><? echo number_format($totalCost,2);?></td>
		<td align="center">
			<input name="paid_<?=$i;?>" type="checkbox" id="paid_<?=$i;?>" value="Y"  <?=$checked?> class="chkBox" onclick="paidAmount()"><input type="hidden" name="alreadyPaid_<?=$i;?>" id="alreadyPaid_<?=$i;?>" value="<? if($checked) echo 'Y';?>">
			<input type="hidden" name="reEdit_<?=$i;?>" value="<?=$edited?>"></td>
              </tr>
              <? } else {   ?>
			  <tr>
			  <td class="listing-item" nowrap height='25' style="padding-left:5px; padding-right:5px;"><?=$challanNo?></td>
			  <td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" align="center"><?=$enteredDate?></td>
			  <td colspan="4" class="err1" style="padding-left:5px; padding-right:5px;">RM Wise settlements are pending </td>
			  </tr>
		<?php 
			}
		?>
			<?php				
			if (sizeof($settlementRecords)==$i && sizeof($costRMArr)>1) {
			?>
		<tr bgcolor="#FFFFFF"> 
			<td class="listing-item" nowrap>&nbsp;</td>
			<td class="listing-head" align="center">TOTAL</td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($costRMArr[$selBillCompanyId],2);?></strong></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($rmSupplyCostArr[$selBillCompanyId],2);?></strong></td>
			<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
			<? echo number_format($grandTotalRMCostArr[$selBillCompanyId],2);?></strong></td>
			<td></td>
		</tr>
		<?php
			} // Sub Total
		?>
		<?php 
			$prevSelBillCompanyId	= $selBillCompanyId;
			$prevBillCompanyId 	= $selBillCompanyId;
		}  // Loop Ends Here
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
		<!--tr bgcolor="#FFFFFF"> 
			<td class="listing-item" nowrap>&nbsp;</td>
			<td class="listing-head" align="center">
				TOTAL<br/>
				<span class="listing-item" style="line-height:normal;font-size:7px;">(<?=$cName?>)</span>
			</td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($costRMArr[$pbId],2);?></strong></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($rmSupplyCostArr[$pbId],2);?></strong></td>
			<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
			<? echo number_format($grandTotalRMCostArr[$pbId],2);?></strong></td>
			<td></td>
		</tr-->
		<?php
			//} // Sub Total
		//} // Arr >0
		?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap>&nbsp;</td>
                <td class="listing-head" align="center">
			<? if (sizeof($prevBillingArr)>0) {?>GR.<? }?> TOTAL
		</td>
                <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($totalRMCost,2);?></strong></td>
		<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($totalRmSupplyCost,2);?></strong></td>
		<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($grandTotalCost,2);?></strong></td>
		<td>&nbsp;</td>
              </tr>
			  
      </table></td><input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>" >
                        </tr>
						<? }?>
                      <tr> 
                        <td colspan="4" align="center" class="err1"><? if(sizeof($settlementRecords)<=0 && $selectSupplier!=""){ echo $msgNoSettlement;}?></td>
                        </tr>
			<? 
			  if(sizeof($settlementRecords)>0){
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
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center">
				<? if($isAdmin==true || $reEdit==true || $companySpecific==true || $edit==true){?>
					<input name="cmdSupplierPayment" type="submit" class="button" id="cmdSupplierPayment" onClick="return validateSettlementSummary(document.frmSettlementSummary);" value=" Save ">
				<? }?>
			</td>
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
