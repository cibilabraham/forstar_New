<?php
	require("include/include.php");
	require_once("lib/RevisePurchaseOrder_ajax.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$addAnother	= false;
	$newRateListCreated = false;
	$layer		= "";
	$userId		= $sessObj->getValue("userId");
	
	//$selection = "?pageNo=".$p["pageNo"]."&supplierFilter=".$p["supplierFilter"]."&supplierRateListFilter=".$p["supplierRateListFilter"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
	
	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
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
	/*-----------------------------------------------------------*/

	
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;	
	}


	if ($g["supplierFilter"]!="") $supplierFilterId = $g["supplierFilter"];
	else $supplierFilterId = $p["supplierFilter"];	

	if ($g["supplierRateListFilter"]!="") $supplierRateListFilterId = $g["supplierRateListFilter"];
	else $supplierRateListFilterId = $p["supplierRateListFilter"];	

	# List all Supplier
	//$supplierRecords	= $supplierMasterObj->fetchAllRecords("INV");	
	$supplierRecords	= $supplierMasterObj->fetchAllRecordsActivesupplier("INV");	
	# List Page Filter
	if ($supplierFilterId) {
		# Get Supplier Rate List
		$supplierRateListFilterRecords = $supplierRateListObj->fetchAllSupplierRateListRecords($supplierFilterId);
	}

	# get Not Received Records
	if ($supplierFilterId!="") {
		$getPORecords = $revisePurchaseOrderObj->getNotRececivedPORecords($supplierFilterId);
	}	

	
	# Revised PO
	if ($p["cmdRevisePOUpdate"]!="") {
		$rowCount	= $p["hidReviseRowCount"];
		$supplierRateListFilterId = $p["supplierRateListFilter"];
		for ($i=1; $i<=$rowCount; $i++) {
			$poMainId = $p["poMainId_".$i];
			$supplierId = $p["supplierId_".$i];
			$poStatus  = $p["hidStatus_".$i];
			# Selected Rate List
				//$currentRateListId = $supplierRateListObj->latestRateList($supplierId);	
				$currentRateListId = $supplierRateListFilterId;
			if ($poMainId!="" && $poStatus!='PC') {							
				$updatePORec = $revisePurchaseOrderObj->updatePORecs($poMainId, $currentRateListId, $supplierId);
			}
			# Patially completed
			if ($poMainId!="" && $poStatus=='PC') {					
				# Get PO Number
				list($isMaxId,$purchaseOrderNo)	= $idManagerObj->generateNumberByType("PO"); 
				# Get all Received Records
				$getPurchaseOrderRecords = $revisePurchaseOrderObj->getPurchaseOrderRecords($poMainId);
				$prevPOId = "";
				foreach ($getPurchaseOrderRecords as $por) {
					$cPOId	   = $por[1];
					$stockId    = $por[2];						
					$orderedQty = $por[4];	
					$receivedQty = $revisePurchaseOrderObj->getReceivedQtyOfStock($stockId, $poMainId);
					$balanceQty = $orderedQty-$receivedQty;				
					if ($balanceQty>0) {
						if ($prevPOId!=$cPOId) {					
							# Add New PO and Updating Current PO Status
					 		$addNewPORec = $revisePurchaseOrderObj->addPurchaseOrder($purchaseOrderNo, $supplierId, $userId, $currentRateListId, $poMainId);
							# Get Last Inserted Id
							$poEntryId = $databaseConnect->getLastInsertedId();	
						}
						# Get current unit Price
						$unitPrice = $revisePurchaseOrderObj->getUnitPrice($supplierId, $stockId, $currentRateListId);
						$totalAmt = $balanceQty * $unitPrice;
						# insert PO Entry Recs
						$poEntryRecIns = $revisePurchaseOrderObj->addPurchaseEntries($poEntryId, $stockId, $unitPrice, $balanceQty, $totalAmt);				
					}
					$prevPOId = $cPOId;
				}
			}
			$urlSelection = "?supplierFilter=".$supplierId."&supplierRateListFilter=".$currentRateListId;
		}
		if ($updatePORec || $addNewPORec) {
			$sessObj->createSession("displayMsg",$msg_succUpdateRevisePO);			
			$sessObj->createSession("nextPage",$url_afterUpdateRevisePO.$urlSelection);
		} else {
			$err = $msg_failUpdateRevisePO;
		}
		$updatePORec	= false;
	}
	
	if ($editMode)	$heading = $label_editSupplierStock;
	else 		$heading = $label_addSupplierStock;
	$ON_LOAD_SAJAX = "Y";
	$ON_LOAD_PRINT_JS	= "libjs/RevisePurchaseOrder.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
	
?>
	<form name="frmRevisePurchaseOrder" action="RevisePurchaseOrder.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="85%" >	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>		
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Revise Purchae Order </td>
	<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
	</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if ($isAdmin) {?>
<input type="submit" value=" Revise PO " class="button"  name="cmdRevisePOUpdate" onclick="return validateRevisePurchaseOrder();">
<? }?>
</td>
											</tr>
										</table>									</td>
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
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3" height="5" >
		<table align="center">
				<TR>
					<TD style="padding-left:10px; padding-right:10px;">
						<table cellpadding="0" cellspacing="0">
        <tr>
		<td nowrap="nowrap">
		<table cellpadding="0" cellspacing="0">
                	<tr>
		<td class="listing-item">Supplier:&nbsp;</td>
                <td>
	<!--<select name="supplierFilter" id="supplierFilter" onchange="this.form.submit();">-->
		<select name="supplierFilter" id="supplierFilter" onchange="functionrevLoad(this);">
		<option value="">-- Select All --</option>
		<?						  
		foreach($supplierRecords as $sr) {
			$supplierId		=	$sr[0];
			$supplierCode		=	stripSlash($sr[1]);
			$supplierName		=	stripSlash($sr[2]);
			$selected ="";
			if ($supplierFilterId==$supplierId) $selected="selected";
		?>
               <option value="<?=$supplierId?>" <?=$selected;?>><?=$supplierName?></option>
                <? }?>
                </select> 
                 </td>
	   <td class="listing-item">&nbsp;</td>
		<td class="listing-item">&nbsp;</td>
	   <td class="listing-item">Rate List:</td>
	<td>
		<select name="supplierRateListFilter" id="supplierRateListFilter">
                        <option value="">-- Select All --</option>
			<?
			foreach ($supplierRateListFilterRecords as $srl) {
				$rateListRecId	=	$srl[0];
				$rateListName	=	stripSlash($srl[1]);				
				$startDate	=	dateFormat($srl[2]);
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected = "";
				if ($supplierRateListFilterId==$rateListRecId) $selected = "Selected";
			?>
                      <option value="<?=$rateListRecId?>" <?=$selected?>><?=$displayRateList?>
                      </option>
                      <? }?>
                      </select>
	</td>		
          <td><!--input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
                            <td class="listing-item" nowrap-->&nbsp;</td>
                          </tr>
                    </table></td></tr></table>
					</TD>
				</TR>
		</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="15" ></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
	<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if (sizeof($getPORecords)>0) {
		$j	=	0;
	?>	
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" width="40" style="padding-left:10px; padding-right:10px;">Revise
		<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'poMainId_'); " class="chkBox">
		</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">PO Id</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Rate List</td>
	</tr>
	<?
		
		foreach ($getPORecords as $pr) {
			$j++;
			$purchaseOrderId = $pr[0];
			$poNumber	 = $pr[1];
			$status		= $pr[2];
			if ($status=='C') {
				$displayStatus	=	"Cancelled";
			} else if ($status=='R') {
				$displayStatus	=	"Received";
			} else if ($status=='PC') {
				$displayStatus	=	"Partially Completed";
			} else  { //($status=='P')
				$displayStatus	=	"Pending";
			}
			$supplierId	= $pr[3];			
			$rateListId = $pr[4];
			# Get Rate List Name
			$usedRateList = $revisePurchaseOrderObj->getRateList($rateListId);
			# Latest Rate List
			$latestRateListId = $supplierRateListObj->latestRateList($supplierId);
			
			$displayUsedRateList= "";
			$displayTitle = "";
			if ($latestRateListId!=$rateListId) {
				$displayUsedRateList = "<span style=\"color:#FF0000\" title=\"$displayTitle\">".$usedRateList."</span>";
				$displayTitle = "This purchase order may need revision as newer rate list exist";
			} else {
				$displayUsedRateList  = $usedRateList;
				$displayTitle = "";
			}
// 			$reviseListRow = "";
// 			if($displayTitle!="")
// 				$reviseListRow ='style="background-color: #ffffff;"  onmouseover="this.style.backgroundColor=\'#E1FFFF\'; ShowTip(\''.$displayTitle.'\')" onmouseout="this.style.backgroundColor=\'#ffffff\'; UnTip()"';
// 			else $reviseListRow = $listRowMouseOverStyle;
			
	?>
	<tr bgcolor="white" >
		<td width="40" align="center">
			<input type="checkbox"  class="chkBox" name="poMainId_<?=$j;?>" id="poMainId_<?=$j;?>" value="<?=$purchaseOrderId;?>" >
			<input type="hidden" name="supplierId_<?=$j;?>" id="supplierId_<?=$j;?>" value="<?=$supplierId;?>" >
			<input type="hidden" name="hidStatus_<?=$j;?>" id="hidStatus_<?=$j;?>" value="<?=$status;?>" >
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$poNumber;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayStatus;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayUsedRateList;?></td>
	</tr>
	<?
		}
	?>		
	<?
		} else if ($supplierFilterId!="") {
	?>
											<tr bgcolor="white">
												<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
<input type="hidden" name="hidReviseRowCount" id="hidReviseRowCount" value="<?=$j?>" >
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="15" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if ($isAdmin) {?>
<input type="submit" value=" Revise PO " class="button"  name="cmdRevisePOUpdate" onclick="return validateRevisePurchaseOrder();">
<? }?>
</td>
											</tr>
										</table>									</td>
								</tr>

								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
<input type="hidden" name="hidSupplierFilterId" value="<?=$supplierFilterId?>">	
<input type="hidden" name="hidSupplierRateListFilterId" value="<?=$supplierRateListFilterId?>">			
		<tr>
			<td height="10"></td>
		</tr>
<input type="hidden" name="pendingPOExist" id="pendingPOExist">
<input type="hidden" name="priceModified" id="priceModified">
<input type="hidden" name="scheduleModified" id="scheduleModified">
	</table>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
<script language="javascript">


</script>