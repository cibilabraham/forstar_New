<?php
	require("include/include.php");
	require_once("lib/SalesOrder_ajax.php");
	ob_start();

	$err			=	"";
	$errDel			=	"";
	$editMode		=	true;
	$addMode		=	false;
	$distributorName	=	"";

	$userId		= $sessObj->getValue("userId");	

	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	//------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
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
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	//----------------------------------------------------------	

	# Update ALL Pending SO Recs
	
	//$updatePendingSO = $changesUpdateMasterObj->updateAllPendingSO();
	//$updateSOMainRec = $changesUpdateMasterObj->updateSalesOrderRec('72', mysqlDateFormat("02/07/2009"));

	$pageRedirection = "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&invoiceType=".$p["invoiceType"]."&selSOId=".$p["selSOId"];

	# select records between selected date
	if ($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}

	if ($g["invoiceType"]!="") $invoiceType = $g["invoiceType"];
	else $invoiceType = $p["invoiceType"];	
	
	if ($g["selSOId"]!="") $selSOId = $g["selSOId"];
	else $selSOId = $p["selSOId"];	

	# New invoice
	//$invoiceNo = $salesOrderObj->getNextInvoiceNo();	
	/*
	if ($p["invoiceDate"]!="") $invoiceDate = $p["invoiceDate"];
	if ($p["invoiceNo"]!="") $invoiceNo = $p["invoiceNo"];
	if ($p["paymentStatus"]!="") $paymentStatus = $p["paymentStatus"];
	if ($p["selTransporter"]) $selTransporter = $p["selTransporter"];
	if ($p["docketNo"])	$docketNo	= $p["docketNo"];
	*/

	if ($selSOId!="") {
		# List all ordered items
		$salesOrderedItemRecs = $orderDispatchedObj->filterSalesOrderRecs($selSOId);
		
		$sORec = $orderDispatchedObj->findSORecord($selSOId);
		$distributorName = $sORec[6];	
		$distributorId	 = $sORec[2];	
		$createDate	 = $sORec[3];	
		$salesOrderNo	 = $sORec[1];	
		$selStatusId	 = $sORec[7];	
		$selPaymentStatus   = $sORec[8];
		$lastDate	 = $sORec[20]; //dateFormat($lastDate)
		$selDispatchDate = ($sORec[9]!="")?dateFormat($sORec[9]):date("d/m/Y");

		$grossWt	 = $sORec[10];
		$invoiceType	= $sORec[18]; // T ->Taxable: S->Sample
		if ($invoiceType=='S') {
			$additionalItemTotalWt = $sORec[19];
			$grossWt += $additionalItemTotalWt;
		}

		$extended	= $sORec[21];
		if ($extended=='E') $extendedChk = "Checked";

		$selTransporter	 = $sORec[11];
		$docketNo	 = $sORec[12];
		$transporterRateListId	= $sORec[13];
		$completeStatus	= $sORec[14];
		$confirmChecked = ($completeStatus=='C')?"Checked":"";	
		$selTaxApplied	= $sORec[15];
		if ($selTaxApplied!="") $taxApplied	= explode(",",$sORec[15]);
		$roundVal      = $sORec[16];
		$salesOrderTotalAmt = ($invoiceType=='T')?round($sORec[17]+$roundVal):100;
		$grandTotalAmt = round($sORec[17]+$roundVal);

		$transOtherChargeRateListId = $sORec[22];

		// Find the Total Amount of Each Sales Order
		//$salesOrderTotalAmt = $salesOrderObj->getSalesOrderAmount($selSOId);
		list($creditLimit, $creditPeriod, $outStandAmt) = $salesOrderObj->getDistMasterRec($distributorId);
		$totOutStandAmt	= $salesOrderTotalAmt+$outStandAmt;
		$disableBtn = "";
		if ($totOutStandAmt>$creditLimit) {
			$err 	= "The selected distributor billed amount is greater than the credit limit.";
			$disableBtn = "disabled";
		}

		$creditPeriodOutStandAmt = $salesOrderObj->getCreditPeriodOutStandAmount($distributorId, $creditPeriod);
		if ($creditPeriodOutStandAmt>$creditLimit) {
			$err1  = "The distributor has ".number_format($creditPeriodOutStandAmt,2,'.','')." outstanding amount.";
			$disableBtn = "disabled";
		}				

		$discount	 = $sORec[23];
		$discountRemark  = $sORec[24];
		$discountPercent = $sORec[25];
		$discountAmt	 = $sORec[26];

		$octroiExempted = $sORec[27];
		$oecNo		= $sORec[28];
		$oecValidDate	= dateFormat($sORec[29]);

		$invoiceNo	= $sORec[1];
		$invoiceDate	= dateFormat($sORec[3]);

		$sampleInvoiceNo = $sORec[33];

		$soNetWt	= $sORec[35]; 
		$soGrossWt	= $sORec[10];
		$soTNumBox	= $sORec[36];	
		$pkngGen	= $sORec[37];
		$pkngConfirmed	= $sORec[38];
		
		if ($invoiceType=='S') $invoiceNo = $sampleInvoiceNo;		

		# List all Transporter		
		$transporterRecords	= $transporterMasterObj->fetchAllRecords();
		$fieldReadOnly ="";
		if ($confirmChecked) $fieldReadOnly = "readonly";
	}
	
	if ($p["hidSOId"]==$selSOId || $p["hidSOId"]=="") {
	
		if ($p["invoiceDate"]!="") $invoiceDate = $p["invoiceDate"];
		//echo "h==$invoiceNo";
		if ($p["invoiceNo"]!="") $invoiceNo = $p["invoiceNo"];
		else if ($invoiceNo==0) $invoiceNo = $salesOrderObj->getNextInvoiceNo();
	
		if ($p["paymentStatus"]!="") $selPaymentStatus = $p["paymentStatus"];
		if ($p["selTransporter"]) $selTransporter = $p["selTransporter"];
		if ($p["docketNo"])	$docketNo	= $p["docketNo"];
		
		if ($p["transporterRateListId"]) $transporterRateListId = $p["transporterRateListId"];
		if ($p["transOtherChargeRateListId"]) $transOtherChargeRateListId = $p["transOtherChargeRateListId"];
	}

	#Update sales Order Rec
	if ($p["cmdSaveChange"]!="") {
		
		$selSOId = $p["selSOId"];
			
		$paymentStatus = $p["paymentStatus"];
		$dispatchDate	= mysqlDateFormat($p["dispatchDate"]);
		$selStatus	= $p["selStatus"];
		$isComplete	= ($p["isComplete"]=="")?'P':$p["isComplete"];
		$selTransporter	= $p["selTransporter"];
		$docketNo	= $p["docketNo"];
		$transporterRateListId = $p["transporterRateListId"];
		$alreadyConfirmed = $p["alreadyConfirmed"];	
		$dateExtended		= $p["dateExtended"];
		$transOtherChargeRateListId = $p["transOtherChargeRateListId"];

		$invoiceNo = $p["invoiceNo"];

		$canUpdate = false;
		if ($alreadyConfirmed=="" || $isAdmin==true || $reEdit==true) {
			$canUpdate = true;
		} 

		$invoiceType = $p["invoiceType"]; // T OR S

		$invceNo = "";
		$invceDate = "";
		if ($isComplete=='C') {
			$invceNo = $p["invoiceNo"];
		}

		$invceDate = mysqlDateFormat($p["invoiceDate"]);
		
		if ($selSOId!="" && $canUpdate) {

			$orderDispatchedRecUptd = $orderDispatchedObj->updateSalesOrder($selSOId, $paymentStatus, $dispatchDate, $selStatus, $isComplete, $selTransporter, $docketNo, $transporterRateListId, $dateExtended, $transOtherChargeRateListId, $invceNo, $invceDate, $invoiceType);
			# Get Dist Account Id
			$distAccountId = $orderDispatchedObj->getDistAccountRec($selSOId);
			if ($orderDispatchedRecUptd && $p["isComplete"]!="" && $alreadyConfirmed=="" && $distAccountId=="" && $invoiceType=='T') {
				$distributorAccountRecIns = $distributorAccountObj->addDistributorAccount($createDate, $distributorId, $salesOrderTotalAmt, 'D', "Sales Invoice No:$invceNo", $userId, $selSOId, '');
			} else if ($distAccountId!="") {
				$updateDistAccount = $orderDispatchedObj->updateDistAccount($distAccountId, $salesOrderTotalAmt);
			}
			
			if ($isComplete=='P' && $alreadyConfirmed!="") {
				# Update Changes
				$updateChanges = $changesUpdateMasterObj->updateSORec($selSOId);

				# Distributor Account Updation (Delete Dist Account Values)
				list($selDistributor, $billAmount, $selCoD) = $salesOrderObj->getDistributorAccountRec($selSOId);
				if ($selDistributor!="" && $billAmount!="") {	
					# Rollback Old Rec
					$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selCoD, $billAmount);
					# delete dist A/c
					$delDistributorAc = $salesOrderObj->delDistributorAccount($selSOId);
				}
			}

			/* Temp Hide
			if ($p["isComplete"]!="") {
				$hidProductRowCount	=	$p["hidProductRowCount"];
				for ($i=1; $i<=$hidProductRowCount; $i++) {
					$selProduct  = $p["selProduct_".$i];
					$existingQty = $p["existingQty_".$i];
					$orderedQty  = $p["orderedQty_".$i];
					if ($existingQty>$orderedQty)  {
						$balanceQty = $existingQty-$orderedQty;
						#Update the Stock
						$updateStockQty = $orderDispatchedObj->updateBalanceStockQty($selProduct, $balanceQty);
					}				
				}
			}
			*/
		}
	
		if ($orderDispatchedRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateOrderDispatched);
			$sessObj->createSession("nextPage",$url_afterUpdateOrderDispatched.$pageRedirection);
		} else {			
			$editMode	=	true;
			$err		=	$msg_failOrderDispatchedUpdate;
		}
		$orderDispatchedRecUptd	=	false;
	}
	

	# Generate Pkng Inst
	if ($p["cmdGenPkngIns"]!="") {		
		$selSOId = $p["selSOId"];

		if ($selSOId && $pkngGen=='N') {
			# Ins Pkng Inst & Update Pkng Gen Flag
			$genPkngInstIns = $orderDispatchedObj->addPackingInstruction($selSOId, $userId);
		}

		if ($genPkngInstIns) {
			$sessObj->createSession("displayMsg", $msg_succGenPkngInst);
			$sessObj->createSession("nextPage",$url_afterUpdateOrderDispatched.$pageRedirection);
		} else {			
			$editMode	=	true;
			if ($pkngGen=='Y') $err = $msg_failGenPkngInst."<br>Packing Instruction already generated.";
			else $err = $msg_failGenPkngInst;
		}
		$genPkngInstIns	=	false;
	}

	#List All Status Record
	//$statusRecords = $statusObj->fetchAllRecords();

	#$List all Sales Order Records
	//$salesOrderPendingRecords = $orderDispatchedObj->fetchNotCompleteRecords();

	if ($editMode) $heading = $label_editOrderDispatched;
	else $heading = $label_addOrderDispatched;
	
	//$help_lnk="help/hlp_king.html";

	$disableBtn = "disabled";

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/OrderDispatched.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmOrderDispatched" id="frmOrderDispatched" action="OrderDispatched.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
			<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<? if($err1!="" ){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err1;?></td>
		</tr>
		<?}?>
		<?php
			if ($selSOId) {
		?>
		<tr><td height="10" align="center"><a href="AssignDocketNo.php" class="link1" title="Click to manage Transporter Docket No.">Assign Docket No</a></td></tr>
		<tr><TD height="10"></TD></tr>
		<? }?>
		<?
		 if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Sales Order Processing</td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center" nowrap="true">
		&nbsp;&nbsp;<? if($add==true){?>
		  <!--<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save & Confirm " onClick="return validateOrderDispatched(document.frmOrderDispatched, true);" style="width:110px" <? if (!$selSOId || $confirmChecked) { echo "disabled";}?> <?=$disableBtn?>>-->
<? }?>&nbsp;&nbsp;
			<? if($print==true){?>
			<!--	PrintSOTaxInvoice// PrintSOTaxInvoiceTest.php	-->
			<input type="button" name="cmdAddSupplierAccount" class="button" value=" Print Tax Invoice " onClick="return printWindow('PrintSOTaxInvoice.php?selSOId=<?=$selSOId?>',700,600);" style="width:120px" <? if (sizeof($salesOrderedItemRecs)==0 || $selSOId=="") echo $disabled="disabled";//if (sizeof($salesOrderedItemRecs)==0 || $confirmChecked=="") echo $disabled="disabled";?>>
			<? if ($octroiExempted=='Y') {?>
			&nbsp;&nbsp;
			<input type="button" name="cmdPrintCSDTaxInvoice" class="button" value=" Print CSD Tax Invoice " onClick="return printWindow('PrintCSDSOTaxInvoice.php?selSOId=<?=$selSOId?>',700,600);" style="width:150px" <? //if (sizeof($salesOrderedItemRecs)==0 || $confirmChecked=="") echo $disabled="disabled";?>>			
			<? }?>
			<!--&nbsp;&nbsp;
			<input type="button" name="btnPrintPackingAdvice" class="button" value=" Print Packing Advice " onClick="return printWindow('PrintSOPackingAdvice.php?selSOId=<?=$selSOId?>',700,600);" style="width:140px" <? if (sizeof($salesOrderedItemRecs)==0 || $selSOId=="") echo $disabled="disabled";?>>-->
			&nbsp;&nbsp;
			<!--<input type="submit" name="cmdGenPkngIns" class="button" value=" Generate Packing Instruction " style="width:210px" onclick="return validateGenPkgIns();" <? if (sizeof($salesOrderedItemRecs)==0 || $selSOId=="") echo $disabled="disabled";?>>-->
		<? }?>
		</td>
		<?} else{?>
		  <input type="hidden" name="cmdAddNew" value="1">
		<?}?>
	</tr>
	<input type="hidden" name="hidPurchaseOrderId" value="<?=$purchaseOrderId;?>">
	<tr>
		  <td nowrap class="fieldName"></td>
	</tr>
	<tr>
		<td colspan="2" style="padding-left:60px;">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
	<table width="65%" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<TD>
			<fieldset>
				<table>
					<TR>
						<TD>
	<table cellpadding="0" cellspacing="0">
                      			<tr>
					  	<td class="fieldName" nowrap="true" style="padding-left:5px;padding-right:2px;">From:</td>
                                    		<td nowrap="true" style="padding-left:2px;padding-right:5px;">	
                            <input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>" onchange="xajax_getSalesOrders(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('invoiceType').value, document.getElementById('selSOId').value);" />
			</td>
				            <td class="fieldName" nowrap="true" style="padding-left:5px;padding-right:2px;">Till:</td>
                                    <td nowrap="true" style="padding-left:2px;padding-right:5px;">
                                      <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>" onchange="xajax_getSalesOrders(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('invoiceType').value, document.getElementById('selSOId').value);" />
					</td>
					   <td class="fieldName" nowrap="true" style="padding-left:5px;padding-right:2px;">Invoice Type:</td>
						<td nowrap="true" style="padding-left:2px;padding-right:5px;">
							<select name="invoiceType" id="invoiceType" onchange="xajax_getSalesOrders(document.getElementById('selectFrom').value, document.getElementById('selectTill').value, document.getElementById('invoiceType').value, document.getElementById('selSOId').value);">
								<option value="T" <? if ($invoiceType=='T') echo "Selected";?> >Taxable</option>
								<option value="S" <? if ($invoiceType=='S') echo "Selected";?> >Sample</option>
							</select>
						</td>
						<td class="fieldName" nowrap="true" style="padding-left:5px;padding-right:2px;">Invoice No:</td>
						<td nowrap="true" style="padding-left:2px;padding-right:5px;">
							<select name="selSOId" id="selSOId" onchange="this.form.submit();">
						  <option value="">-- Select --</option>
						 <?php
						foreach ($salesOrderPendingRecords as $por) {
							$soId	      =	$por[0];
							$soGenerateId =	$por[1];
							$selected="";
							if($selSOId==$soId) $selected="Selected";
						?>
						<option value="<?=$soId?>" <?=$selected?>><?=$soGenerateId?></option>
						<? }?>
                                                      </select>
						</td>
					        <!--<td nowrap="true" style="padding-left:5px;padding-right:5px;">
							<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search">
						</td>-->
                          </tr>
                    </table>
							
						</TD>
					</TR>
				</table>
		</fieldset>
			</TD>
		</tr>
        	<tr>
                	<td valign="top">
			<table width="200">
                              <!--<tr>
                        	       <td class="fieldName" nowrap="nowrap">Sales Order Id </td>
                                        <td align="left">
						  <select name="selSOId" id="selSOId" onchange="this.form.submit();">
						  <option value="">-- Select --</option>
						 <?
						foreach ($salesOrderPendingRecords as $por) {
							$soId	      =	$por[0];
							$soGenerateId =	$por[1];
							$selected="";
							if($selSOId==$soId) $selected="Selected";
						?>
						<option value="<?=$soId?>" <?=$selected?>><?=$soGenerateId?></option>
						<? }?>
                                                      </select>                                                      </td>
                                                    </tr>-->
						<? if($selSOId){?>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Distributor</td>
                                                      <td class="listing-item" nowrap="true" align="left">&nbsp;<?=$distributorName?></td>
                                                    </tr>
						<? }?>
						</table></td>
                                                  <td valign="top">&nbsp;</td>
                                                </tr>
                                              </table>
					</td>
				</tr>
	<?php
		if($selSOId){
	?>	
	<tr>
		  <td colspan="2" align="center">
	<table cellpadding="1"  width="65%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
	$i = 0;
	if (sizeof($salesOrderedItemRecs)>0) {		
	?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Sr.No</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">DESCRIPTION OF GOODS</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">M/C</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">IND<br/> PKTS</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">TOTAL<br/> PKTS</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">FREE<br/>PKTS</td>
		<!--<td class="listing-head" style="padding-left:10px; padding-right:10px;">Existing<br>Qty</td>-->
		<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">RATE PER <br/>UNIT (RS.)</td>
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">TOTAL<br/>(RS.)</td>
	</tr>
	<?
	$totalAmount = 0;
	$totalNumMCPack = 0;
	$totalNumLoosePack = 0;
	$totalQuantity = 0;
	$totalFreePkts = 0;
	foreach ($salesOrderedItemRecs as $sor) {
		$i++;		
		$prodRate	= $sor[3];
		$prodQty	= $sor[4];
		$totalQuantity  += $prodQty;
		$prodTotalAmt 	= $sor[5];
		$totalAmount 	+= $prodTotalAmt;
		$selProductId	= $sor[2];
		$productName	= "";
		$productRec	= $manageProductObj->find($selProductId);
		$productName	= $productRec[2];
		$existingQty = "";
		//$existingQty = $orderDispatchedObj->getProductExistingQty($selProductId);
		$numMCPack	= $sor[7];
		$totalNumMCPack += $numMCPack;
		$numLoosePack	= $sor[8];
		$totalNumLoosePack += $numLoosePack;
		$freePkts = $sor[13];
		$totalFreePkts += $freePkts;
	?>
	<tr  bgcolor="WHITE"  >
		<td height="20" align="center" class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap="true">
			<?=$i?>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap="true">
			<?=$productName?>
			<input type="hidden" name="selProduct_<?=$i?>" id="selProduct_<?=$i?>" value="<?=$selProductId?>">
		</td>
		<!--<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<?=$existingQty?>
			<input type="hidden" name="existingQty_<?=$i?>" id="existingQty_<?=$i?>" value="<?=$existingQty?>">
		</td>-->
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$numMCPack?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$numLoosePack?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right">
			<?//$prodQty?>
			<?=number_format($prodQty,0,'.','');?>
			<input type="hidden" name="orderedQty_<?=$i?>" id="orderedQty_<?=$i?>" value="<?=$prodQty?>">
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$freePkts?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$prodRate?></td>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$prodTotalAmt?></td>		
	</tr>
	<?
		}
	?>					
	<tr bgcolor="white">
		<td>&nbsp;</td>
		<!--<td>&nbsp;</td>-->
		<td class="listing-head" align="right">Total:</td>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><strong><?=$totalNumMCPack?></strong></td>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><strong><?=$totalNumLoosePack?></strong></td>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><strong><?=$totalQuantity?></strong></td>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><strong><?=$totalFreePkts?></strong></td>
        	<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
                <td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><strong><?=number_format($totalAmount,2,'.','');?></strong></td>
	</tr>
	<?php
		if ($discount=='Y') {
	?>
	<!--<tr bgcolor="white">		
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;" colspan="6"><?=$discountRemark?></td>
        	<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;font-size:8pt">DISCOUNT</td>
                <td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><strong><?=$discountPercent;?>&nbsp;%</strong></td>
	</tr>-->
	<tr bgcolor="white">		
		<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt;" colspan="6"><?=$discountRemark?></td>
        	<td class="listing-head" style="padding-left:3px; padding-right:3px; font-size:9px;line-height:normal;" align="right" nowrap="true">(Less) <br/>DISCOUNT&nbsp;<?=$discountPercent;?>%</td>
                <td class="listing-item" align="right" style="padding-left:10px; padding-right:10px; font-size:8pt;"><strong><?=$discountAmt?></strong></td>
	</tr>
	<?php
		}
	?>
	<?php
	if (sizeof($taxApplied)>0) {	
		for ($j=0;$j<sizeof($taxApplied);$j++) {
			$selTax = explode(":",$taxApplied[$j]); // Tax Percent:Amt
	?>
	<tr bgcolor="#FFFFFF">
		<td height='20' colspan="6" nowrap="nowrap" class="listing-head" align="right"></td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" align="right" nowrap="true">Add:&nbsp; <?=$taxType?> <?=$selTax[0]?>%</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px; font-size:8pt;" align="right"><strong><?=$selTax[1];?></strong></td>
      </tr>
	<?php
		}	// For Loop Ends Here
	}
	?>
	<tr bgcolor="#FFFFFF">
		<td height='20' colspan="6" nowrap="nowrap" class="listing-head" align="right">
			<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
				<tr>
					<TD class="print-listing-head" height="20" style="padding-left:5px;padding-right:5px;">NET WT:&nbsp;<span class="listing-item"><strong><?=$soNetWt;?>&nbsp;Kg</strong></span></TD>
					<TD class="print-listing-head" style="padding-left:5px;padding-right:5px;">GR. WT:&nbsp;<span class="listing-item"><strong><?=$soGrossWt;?>&nbsp;Kg</strong></span></TD>
					<TD class="print-listing-head" style="padding-left:5px;padding-right:5px;">NO.OF BOXES:&nbsp;<span class="listing-item"><strong><?=$soTNumBox;?></strong></span></TD>
				</tr>
			</table>
		</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" align="right">Round</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px; font-size:8pt;" align="right"><strong><?=$roundVal?></strong></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td height='20' colspan="6" nowrap="nowrap" class="listing-head" align="right">
			
		</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" align="right">Gr. Total</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px; font-size:8pt;" align="right"><strong><? echo number_format($grandTotalAmt,2);?></strong></td>
      </tr>
	<? 
		} else { 
	?>
	<tr bgcolor="white">
		<td colspan="8"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
											<?
												}
											?>
	<input type="hidden" name="hidProductRowCount" id="hidProductRowCount" value="<?=$i?>" >
										</table></td>
										  </tr>
										  <? }?>
										  <!-- Here -->
											<tr>
											  <td colspan="2" align="center">&nbsp;</td>
										  </tr>
		<? if($selSOId){?>
		<tr>
			<td colspan="2" align="center">
			<table width="200" border="0" cellpadding="0" cellspacing="0">
                        <!--<tr>
                                <td valign="top"><table width="200" border="0">
                                                    <tr>
                                                      <td nowrap class="fieldName">Payment Description:</td>
                                                      <td nowrap class="listing-item">
                                                        <input name="paymentStatus" type="text" id="paymentStatus" value="<?=$selPaymentStatus?>">
							</td>
                                                    </tr>
						    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Date of Dispatch :</td>
                                                      <td>
								<input type="text" name="dispatchDate" id="dispatchDate"  size="8" value="<?=$selDispatchDate?>" autoComplete="off">
							</td>
                                                    </tr>	
                                                  </table></td>
                                                  <td valign="top">
						<table width="200" border="0">
                                                    <tr>
                                                      <td nowrap="nowrap" class="fieldName">Status</td>
                                                      <td nowrap="nowrap" class="fieldName">
						<table width="200" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                      <td nowrap="nowrap">
						  <select name="selStatus" id="selStatus">
						  <option value="">-- Select --</option>
					  <?
					/*
					 foreach ($statusRecords as $sr) {
						$statusId	= $sr[0];
						$status		= stripSlash($sr[1]);
						$selected	= "";
						if ($selStatusId==$statusId) $selected = " Selected ";
					*/
					?>
					<option value="<?=$statusId?>" <?=$selected?>><?=$status?></option>
						<? //}?>
                                                     </select>                                                      </td>
                                                      <td nowrap="nowrap" ><table width="100">
                                                        <tr>
                                                          <td align="right"><input name="isComplete" type="checkbox" id="isComplete" value="C" class="chkBox"></td>
                                                          <td class="listing-item">Confirm</td>
                                                        </tr>
                                                      </table>                                                        </td>
                                                    </tr>
                                                  </table></td>
                                                    </tr>
                                                  </table></td>
                                                  <td>&nbsp;</td>
                                                </tr>-->
	<tr>
		<TD align="left" valign="top">
			<table cellpadding="0" cellspacing="0">
				<TR>
				<TD>
					<fieldset>
					<legend class="listing-item" style="line-height:normal;">Despatch Details</legend>
					<table>
						<TR>
							<TD class="fieldName" nowrap="true">*Sent Through:</TD>
							<td>
								<select name="selTransporter" id="selTransporter" onchange="xajax_checkValidTransporter(document.getElementById('selTransporter').value, document.getElementById('dispatchDate').value);">
                                        			<option value="">-- Select --</option>
								<?php
									foreach ($transporterRecords as $tr) {
										$transporterId	 = $tr[0];
										$transporterName = stripSlash($tr[2]);	
										$selected = "";
										if ($selTransporter==$transporterId) $selected = "selected";	
								?>
								<option value="<?=$transporterId?>" <?=$selected?>><?=$transporterName?></option>
								<? }?>
								</select>
							</td>
						</TR>
						<?php
						if ($docketNo) {
						?>
						<tr>
							<TD class="fieldName" nowrap="true">Transporter Docket No:</TD>
							<td class="listing-item" nowrap="true">
								<?=$docketNo?>
								<!--<input type="text" name="docketNo" id="docketNo" value="<?=$docketNo?>" size="12">-->
							</td>
						</tr>
						<? } ?>
						<!--<tr>
							<TD class="fieldName" nowrap="true">Octroi:</TD>
							<td>
								<table>
									<TR>
										<TD>
											<input name="octroiExist" type="checkbox" id="octroiExist" value="Y" <?=$octroiExistChk?> class="chkBox">
										</TD>
										<td class="fieldName" style="vertical-align:middle; line-height:normal;font-size:7px;">(If Yes, please give tick mark)</td>
									</TR>
								</table>
							</td>
						</tr>-->
						<tr>
							<TD class="fieldName" nowrap="true">Gross Wt:</TD>
							<td class="listing-item"><?=$grossWt?>&nbsp;Kg</td>
						</tr>
					</table>
					</fieldset>
				</TD>
				</TR>
			</table>
		</TD>
		<td valign="top">
			<table>
				<TR>
				<TD>
					<fieldset>					
					<table>
						   <tr>
                                                      <td nowrap class="fieldName">Payment Description:</td>
                                                      <td nowrap class="listing-item">
                                                        <input name="paymentStatus" type="text" id="paymentStatus" value="<?=$selPaymentStatus?>">
							</td>
                                                    </tr>
						    <!--<tr>
                                                      <td class="fieldName" nowrap="nowrap">Invoice Date:</td>
                                                      <td align="left" class="listing-item" nowrap="true">
								<?=dateFormat($createDate);?>
						      </td>
                                                    </tr>-->
	<tr>
		<TD colspan="2">
			<table>
				<TR>
					 <td class="fieldName" nowrap="true">*Invoice No.</td>
					 <td class="listing-item" nowrap="true">
						<input name="invoiceNo" id="invoiceNo" type="text" size="6" onKeyUp="xajax_chkSONumberExist(document.getElementById('invoiceNo').value, '<?=$mode?>', '<?=$selSOId?>', document.getElementById('invoiceDate').value, document.getElementById('invoiceType').value);" value="<?=($invoiceNo!=0)?$invoiceNo:"";?>" onchange="updateSOMainRec('<?=$selSOId?>',document.getElementById('invoiceDate').value);" autocomplete="off" <?=$fieldReadOnly?>/>
						<br/>
						<span id="divSOIdExistTxt" style='line-height:normal; font-size:10px; color:red;'></span>
					</td>
					<td class="fieldName" nowrap="true">*Invoice Date</td>
						<td nowrap="true">
						<?php
							if ($invoiceDate=="") $invoiceDate = date("d/m/Y");
						?>
						<input type="text" name="invoiceDate" id="invoiceDate" value="<?=$invoiceDate?>" size="8" onchange="xajax_chkValidInvoiceDate(document.getElementById('invoiceDate').value); updateSOMainRec('<?=$selSOId?>',document.getElementById('invoiceDate').value);" autocomplete="off" <?=$fieldReadOnly?>/>
						<input type="hidden" name="validInvoiceDate" id="validInvoiceDate" value="">
						</td>
				</TR>
			</table>
		</TD>
	</tr>
	<tr>
               <td class="fieldName" nowrap="nowrap">*Date of Despatch :</td>
		<td align="left" nowrap="true">
			<!--<input type="text" name="dispatchDate" id="dispatchDate"  size="8" value="<?=$selDispatchDate?>" autoComplete="off" onchange="xajax_chkValidDespatchDate(document.getElementById('dispatchDate').value);">
			<input type="hidden" name="validDespatchDate" id="validDespatchDate" value="">-->
			<table cellpadding="0" cellspacing="0">
			<TR>
				<TD>
					<input type="text" name="dispatchDate" id="dispatchDate"  size="8" value="<?=$selDispatchDate?>" autoComplete="off" onchange="xajax_chkValidDespatchDate(document.getElementById('dispatchDate').value);" <?=$fieldReadOnly?>>
					<input type="hidden" name="validDespatchDate" id="validDespatchDate" value="">
					<input type="hidden" name="lastDateStatus" id="lastDateStatus" value="<?=dateFormat($lastDate)?>">
				</TD>
				<td>
					<table width="100">
					<tr>
						<td>
							<input name="dateExtended" type="checkbox" id="dateExtended" value="E" class="chkBox" <?=$extendedChk?>>
						</td>
						<td class="listing-item">Extended</td>
					</tr>
					</table>
				</td>
			</TR>
			</table>
		</td>
        </tr>
	<tr style="display:none;">
                <td nowrap="nowrap" class="fieldName" align="left">*Status</td>
                <td nowrap="nowrap" class="fieldName">
						<table width="200" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                      <td nowrap="nowrap" style="display:none;">
						  <select name="selStatus" id="selStatus">
						  <option value="">-- Select --</option>
					  <?
					 foreach ($statusRecords as $sr) {
						$statusId	= $sr[0];
						$status		= stripSlash($sr[1]);
						$selected	= "";
						if ($selStatusId==$statusId) $selected = " Selected ";
					?>
					<option value="<?=$statusId?>" <?=$selected?>><?=$status?></option>
						<? }?>
                                                     </select>                                                      </td>
                                                      <td nowrap="nowrap" align="left" >
							<table width="100" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                          <td align="left">
								<input name="isComplete" type="text" id="isComplete" value="C" class="chkBox" <?//$confirmChecked?>>
								<input type="hidden" name="alreadyConfirmed" id="alreadyConfirmed" value="<? if($confirmChecked) echo 'Y';?>">
							</td>
                                                          <td class="listing-item">Confirm</td>
                                                        </tr>
                                                      </table>                                                        </td>
                                                    </tr>
                                                  </table></td>
                                                    </tr>		
					</table>
					</fieldset>
				</TD>
				</TR>
			</table>
		</td>
	</tr>
                                              <tr>
                                                  <td colspan="3"></td>
                                                </tr>
                                              </table></td>
				  </tr>
			  <? }?>
			  <!-- Here-->
	<tr>
	  <td align="center">&nbsp;</td>
	  <td align="center">&nbsp;</td>
	</tr>
	<tr>		
		<td colspan="2" align="center">&nbsp;&nbsp;
		<? if($add==true){?>
		  <!--<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save & Confirm " onClick="return validateOrderDispatched(document.frmOrderDispatched, true);" style="width:110px" <? if (!$selSOId || $confirmChecked) { echo "disabled";}?> <?=$disableBtn?>>-->
		<? }?>
	&nbsp;&nbsp;
		<? if($print==true){?>
			<input type="button" name="cmdAddSupplierAccount" class="button" value=" Print Tax Invoice " onClick="return printWindow('PrintSOTaxInvoice.php?selSOId=<?=$selSOId?>',700,600);" style="width:120px" <? if (sizeof($salesOrderedItemRecs)==0 || $selSOId=="") echo $disabled="disabled";//if (sizeof($salesOrderedItemRecs)==0 || $confirmChecked=="") echo $disabled="disabled";?>>
			<? if ($octroiExempted=='Y') {?>
			&nbsp;&nbsp;
			<input type="button" name="cmdPrintCSDTaxInvoice" class="button" value=" Print CSD Tax Invoice " onClick="return printWindow('PrintCSDSOTaxInvoice.php?selSOId=<?=$selSOId?>',700,600);" style="width:150px" <? //if (sizeof($salesOrderedItemRecs)==0 || $confirmChecked=="") echo $disabled="disabled";?>>
			<? }?>
			<!--&nbsp;&nbsp;
			<input type="button" name="btnPrintPackingAdvice" class="button" value=" Print Packing Advice " onClick="return printWindow('PrintSOPackingAdvice.php?selSOId=<?=$selSOId?>',700,600);" style="width:140px" <? if (sizeof($salesOrderedItemRecs)==0 || $selSOId=="") echo $disabled="disabled";?>>-->
			&nbsp;&nbsp;
			<!--<input type="submit" name="cmdGenPkngIns" class="button" value=" Generate Packing Instruction " style="width:210px" onclick="return validateGenPkgIns();" <? if (sizeof($salesOrderedItemRecs)==0 || $selSOId=="") echo $disabled="disabled";?>>-->
		<? }?>
		</td>
		
	</tr>
	<tr>
		<td  height="10" ></td>
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
		<?
			}
			
			# Listing Grade Starts
		?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td><!-- Form fields end   --></td>
		</tr>	
<input type="hidden" name="salesOrderItem" value="<?=sizeof($salesOrderedItemRecs)?>">	
<input type="hidden" name="transporterRateListId" id="transporterRateListId" value="<?=$transporterRateListId?>">
<input type="hidden" name="transOtherChargeRateListId" id="transOtherChargeRateListId" value="<?=$transOtherChargeRateListId?>">	
<input type="hidden" name="hidSOId" id="hidSOId" value="<?=$selSOId?>"/> 

		<tr>
			<td height="10"></td>
		</tr>	
	</table>
	<script language="JavaScript" type="text/javascript">
		xajax_getSalesOrders('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$invoiceType?>', '<?=$selSOId?>');	
		//xajax_chkSONumberExist(document.getElementById('invoiceNo').value, '<?=$mode?>', '<?=$selSOId?>', document.getElementById('invoiceDate').value, document.getElementById('invoiceType').value);
	</script>	
	<? if ($invoiceDate && !$confirmChecked) { ?>
		<script language="JavaScript" type="text/javascript">
			xajax_chkValidInvoiceDate('<?=$invoiceDate?>');
			xajax_chkSONumberExist(document.getElementById('invoiceNo').value, '<?=$mode?>', '<?=$selSOId?>', document.getElementById('invoiceDate').value, document.getElementById('invoiceType').value);
		</script>
	<? }?>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dispatchDate",         // ID of the input field
			eventName   : "click",	    // name of event
			button : "dispatchDate", 
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
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "invoiceDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "invoiceDate", 
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
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>