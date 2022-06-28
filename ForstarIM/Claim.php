<?php
	require("include/include.php");
	require("lib/Claim_ajax.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$userId		= $sessObj->getValue("userId");
	$selSOId	= "";

	$dateSelection =  "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
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

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") $addMode = false;


	$genClaimId = $idManagerObj->check("DC");

	$selSOId = $p["selSalesOrder"];

	if ($selSOId!="" && $addMode!="") {
		# List all ordered items
		$salesOrderedItemRecs = $claimObj->filterSalesOrderRecs($selSOId);
		// Find the distributor Name
		$sORec = $orderDispatchedObj->findSORecord($selSOId);
		$distributorName = $sORec[6];
	}

	/*
	# Claim Type setting
		MR -> material Return
		FA -> Fixed Amt
	*/
	$claimType	= $p["claimType"];
	if ($claimType=='MR') $materialReturnChk = "checked";
	if ($claimType=='FA') $fixedAmtChk	= "checked";
		
	
	#Add a Record
	if ($p["cmdAdd"]!="") {

		if ($genClaimId==1) {
			list($isMaxId,$claimOrderNo)	= $idManagerObj->generateNumberByType("DC"); 
			$warning  = ( $isMaxId=="Y") ? "The generated Claim ID is greater than the ending number of Purchase Order ID." : "";
			$chkClaimNumExist = $claimObj->checkClaimNumberExist($claimOrderNo);
		} else {
			$claimOrderNo = $p["claimNumber"];
			$isMax = $idManagerObj->checkMaxId("DC",$claimOrderNo);
			
			if( $isMax=="Y") $warning = "The generated Claim ID is greater than the ending number of Purchase Order ID.";
			
		}
	
		$lastDate	= mysqlDateFormat($p["lastDate"]);

		$tableRowCount = $p["hidTableRowCount"];
		$claimType	= $p["claimType"];
		$debit		= ($p["debit"]=="")?C:$p["debit"];
		$toalClaimAmt	= ($p["toalClaimAmt"]=="")?0:$p["toalClaimAmt"];		

		$grandTotalReturnAmt = 0;
		if ($claimType=='MR') {
			 $distributorId = $p["selDistributor"];
			 $grandTotalReturnAmt = $p["grandTotalReturnAmt"];
		}
		else if ($claimType=='FA') $distributorId = $p["selFADistributor"];

		$fixedAmtReason	= addSlash(trim($p["fixedAmtReason"]));
		//$selSalesOrderId = $p["selSalesOrder"];
		
				
		if ($claimOrderNo  && !$chkClaimNumExist) {

			if ($claimType!="" && $claimOrderNo!="") {
				$claimRecIns = $claimObj->addClaim($claimOrderNo, $userId, $lastDate, $claimType, $debit, $distributorId, $toalClaimAmt, $grandTotalReturnAmt, $fixedAmtReason);
				#Find the Last inserted Id From t_claim
				$claimMainEntryId = $databaseConnect->getLastInsertedId();
			}
			# If Claim Type = Material Return 
		       if ($claimType=='MR') {
				for ($i=0; $i<$tableRowCount; $i++) {
					$status 	= $p["status_".$i];
					$itemCount	= $p["hidItemCount_".$i];
				if ($status != 'N' ) {	
					$selSalesOrderId = $p["selSalesOrder_".$i];
					if ($claimMainEntryId!="" && $selSalesOrderId!="") {
						$claimSORecIns = $claimObj->addClaimSORec($claimMainEntryId, $selSalesOrderId);
						# Find the Last SO Entry Id
						$claimSOEntryId = $databaseConnect->getLastInsertedId();
					for ($j=1; $j<=$itemCount; $j++) {
						$salesOrderEntryId = $p["salesOrderEntryId_".$j."_".$i];
						$selProductId	=	$p["selProduct_".$j."_".$i];
						$unitPrice	=	trim($p["unitPrice_".$j."_".$i]);
						$quantity	=	trim($p["quantity_".$j."_".$i]);
						$totalAmt	=	$p["totalAmount_".$j."_".$i];
						$defectQty	=	$p["defectQty_".$j."_".$i];
						$defectType	=	$p["defectType_".$j."_".$i];
//echo "$claimSOEntryId, $selProductId, $unitPrice, $quantity, $totalAmt, $defectQty, $defectType,$salesOrderEntryId";
						if ($claimSOEntryId!="" && $selProductId!="") {
							$salesOrderItemsIns = $claimObj->addClaimEntries($claimSOEntryId, $selProductId, $unitPrice, $quantity, $totalAmt, $defectQty, $defectType, $salesOrderEntryId);
						}
					}
					}
				}
			}
		 }
			
		}

		if ($claimRecIns) {
			if ($warning !="") {
		?>
			<SCRIPT LANGUAGE="JavaScript">
			<!--
				alert("<?=$warning;?>");
			//-->
			</SCRIPT>
		<?
			}
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddClaim);
			$sessObj->createSession("nextPage",$url_afterAddClaim.$dateSelection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddClaim;
		}
		$claimRecIns		=	false;
	}
	

	# Edit a Record
	if ($p["editId"]!="" ) {
		$editId			= $p["editId"];
		$editMode		= true;
		$claimRec		= $claimObj->find($editId);		
		$editClaimId		= $claimRec[0];		
		$editClaimOrder		= $claimRec[1];

		/*
		if ($p["editSelectionChange"]=='1' || $p["selSalesOrder"]=="") {
			$selSOId = $claimRec[2];
		} else {
			$selSOId = $p["selSalesOrder"];
		}
		*/	
		
		$lastDate	= dateFormat($claimRec[4]);

		$extended	= $claimRec[6];
		if ($extended=='E') $extendedChk = "Checked";

		# List all Claim items and ordered items
		//$salesOrderedItemRecs = $claimObj->filterClaimRecs($selSOId);
		// Find the distributor Name
		$sORec = $orderDispatchedObj->findSORecord($selSOId);
		$distributorName = $sORec[6];

		if ($selSOId) $disableSO = "disabled";

		$claimType	= $claimRec[6];
		if ($claimType=='MR') $materialReturnChk = "checked";
		if ($claimType=='FA') $fixedAmtChk	= "checked";
		$selDistributorId	= $claimRec[8];
		$cod 			= $claimRec[7];
		if ($cod=='D') $debitChk = "checked";

		$salesOrderRecords = $claimObj->fetchAllDistWiseSORecords($selDistributorId);
		$getSelectedSORecords = $claimObj->getselectedSalesOrderRecs($editClaimId);
		$toalClaimAmt	= $claimRec[9];

		$fixedAmtReason	= stripSlash($claimRec[10]);
	}


	#Update Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$claimOrderId = $p["hidClaimOrderId"];

		//$itemCount	= $p["hidItemCount"];
		//$selSalesOrderId = $p["selSalesOrder"];
		//$selSalesOrderId = $p["hidSelSOId"];	
		$lastDate	= mysqlDateFormat($p["lastDate"]);
		$dateExtended	=	$p["dateExtended"];		
		$claimOrderNo = $p["claimNumber"];
		$hidClaimNumber	= $p["hidClaimNumber"];
		if ($genClaimId==0 && ($claimOrderNo!=$hidClaimNumber ) )  {
			$chkUnique = $claimObj->checkUnique($claimOrderNo, $hidClaimNumber);
		}

		$tableRowCount = $p["hidTableRowCount"];
		$claimType	= $p["claimType"];
		$debit		= ($p["debit"]=="")?C:$p["debit"];
		$toalClaimAmt	= ($p["toalClaimAmt"]=="")?0:$p["toalClaimAmt"];		

		$grandTotalReturnAmt = 0;
		if ($claimType=='MR') 	{
		   	$distributorId = $p["selDistributor"];
			$grandTotalReturnAmt = $p["grandTotalReturnAmt"];
		}
		else if ($claimType=='FA') $distributorId = $p["selFADistributor"];

		$fixedAmtReason	= addSlash(trim($p["fixedAmtReason"]));				

		if ($claimOrderId!="" && !$chkUnique) {
			$claimRecUptd = $claimObj->updateClaim($claimOrderId, $lastDate, $dateExtended, $claimType, $debit, $toalClaimAmt, $distributorId, $grandTotalReturnAmt, $fixedAmtReason);
			
			# If Claim Type = Material Return 
		       if ($claimType=='MR') {
				for ($i=0; $i<$tableRowCount; $i++) {
					$status 		= $p["status_".$i];
					$itemCount		= $p["hidItemCount_".$i];
					$hidClaimSOEntryId 	= $p["hidClaimSOEntryId_".$i];
					// Delete all Entries Coresponding to the SO Entry
					if ($status=='N' && $hidClaimSOEntryId!="") {
						$deleteSOEntry = $claimObj->deleteSOEntryRec($hidClaimSOEntryId);
						$deleteClaimEntries =  $claimObj->deleteClaimEntriesRec($hidClaimSOEntryId);
					}
			
				if ($status!='N') {	
					$selSalesOrderId = $p["selSalesOrder_".$i];
					if ($claimOrderId!="" && $selSalesOrderId!="") {
						if ($hidClaimSOEntryId=="") {
							$claimSORecIns = $claimObj->addClaimSORec($claimOrderId, $selSalesOrderId);
							# Find the Last SO Entry Id
							$claimSOEntryId = $databaseConnect->getLastInsertedId();
						} else if ($hidClaimSOEntryId!="") {
							$claimSORecUpdate = $claimObj->updateClaimSORec($hidClaimSOEntryId, $selSalesOrderId) ;
							$claimSOEntryId = $hidClaimSOEntryId;
						}
						
					for ($j=1; $j<=$itemCount; $j++) {
						$claimEntryId	=	$p["claimEntryId_".$j."_".$i];
						$salesOrderEntryId = $p["salesOrderEntryId_".$j."_".$i];
						$selProductId	=	$p["selProduct_".$j."_".$i];
						$unitPrice	=	trim($p["unitPrice_".$j."_".$i]);
						$quantity	=	trim($p["quantity_".$j."_".$i]);
						$totalAmt	=	$p["totalAmount_".$j."_".$i];
						$defectQty	=	$p["defectQty_".$j."_".$i];
						$defectType	=	$p["defectType_".$j."_".$i];

						$hidSelProduct	= $p["hidSelProduct_".$j."_".$i]; // selected but Unselected
						//echo "$selProductId,$hidSelProduct,$claimEntryId<br>";
						if ($selProductId=="" && $hidSelProduct!="" && $claimEntryId!="") {
							
							#delete Unselected Product from the Claim Entry table
							$delClaimRec = $claimObj->delClaimEntryRec($claimEntryId);
						}

						if ($claimOrderId!="" && $selProductId!="" && $unitPrice!="" && $quantity!="" && $claimEntryId=="") {
							#when new record -add
								$salesOrderItemsIns = $claimObj->addClaimEntries($claimSOEntryId, $selProductId, $unitPrice, $quantity, $totalAmt, $defectQty, $defectType, $salesOrderEntryId);
						} else if ($claimOrderId!="" && $selProductId!="" && $unitPrice!="" && $quantity!="" && $claimEntryId!="") {
							// When existing rec- Update
							$updateclaimRec = $claimObj->updateClaimEntries($claimEntryId, $selProductId, $unitPrice, $quantity, $totalAmt, $defectQty, $defectType, $salesOrderEntryId);
						}
					   }
					}
				}
			}
		 }


			/*
			for ($i=1; $i<=$itemCount; $i++) {

				$claimEntryId	=	$p["claimEntryId_".$i];
				$salesOrderEntryId = $p["salesOrderEntryId_".$i];
				$selProductId	=	$p["selProduct_".$i];
				$unitPrice	=	trim($p["unitPrice_".$i]);
				$quantity	=	trim($p["quantity_".$i]);
				$totalAmt	=	$p["totalAmount_".$i];
				$defectQty	=	$p["defectQty_".$i];
				$defectType	=	$p["defectType_".$i];
		
				$hidSelProduct	= $p["hidSelProduct_".$i]; // selected but Unselected
				if ($selProductId=="" && $hidSelProduct!="" && $claimEntryId!="") {
					#delete Unselected Product from the Claim Entry table
					$delClaimRec = $claimObj->delClaimEntryRec($claimEntryId);
				}

				if ($claimOrderId!="" && $selProductId!="" && $unitPrice!="" && $quantity!="" && $claimEntryId=="") {
					#when new record -add
					$salesOrderItemsIns = $claimObj->addClaimEntries($claimOrderId, $selProductId, $unitPrice, $quantity, $totalAmt, $defectQty, $defectType, $salesOrderEntryId);
				} else if ($claimOrderId!="" && $selProductId!="" && $unitPrice!="" && $quantity!="" && $claimEntryId!="") {
					// When existing rec- Update
					$updateclaimRec = $claimObj->updateClaimEntries($claimEntryId, $selProductId, $unitPrice, $quantity, $totalAmt, $defectQty, $defectType, $salesOrderEntryId);
				}
			}
			*/
		}
	
		if ($claimRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succClaimUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateClaim.$dateSelection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failClaimUpdate;
		}
		$claimRecUptd	=	false;
	}
	

	# Delete a Record
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$claimOrderId	=	$p["delId_".$i];
			$hidClaimOrderStatus = $p["hidClaimOrderStatus_".$i];
			
			if ($claimOrderId!="" && $hidClaimOrderStatus=="") {

				/****/
				list($selDistributor, $billAmount, $selCoD) = $claimObj->getDistributorAccountRec($claimOrderId);
				if ($selDistributor!="" && $billAmount!="") {	
					# Rollback Old Rec
					$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selCoD, $billAmount);
					# delete dist A/c
					$delDistributorAc = $claimObj->delDistributorAccount($claimOrderId);
				}

				/****/
				
				
				// Delete Claim order main (Delete all table entries)
				$claimRecDel = $claimObj->deleteClaim($claimOrderId);
			}
		}
		if ($claimRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelClaim);
			$sessObj->createSession("nextPage",$url_afterDelClaim.$dateSelection);
		} else {
			$errDel	=	$msg_failDelClaim;
		}
		$claimRecDel	=	false;
	}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------		

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

	#List all Purchase Order
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$claimRecords = $claimObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);		
		$claimRecordSize = sizeof($claimRecords);
		// For Pagination
		$fetchAllClaim = $claimObj->fetchAllRecords($fromDate, $tillDate);
	}

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllClaim);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	# List all Distributor
	//$distributorResultSetObj = $distributorMasterObj->fetchAllRecords();
	//$distResultSetObj = $distributorMasterObj->fetchAllRecords();
	$distributorResultSetObj = $distributorMasterObj->fetchAllRecordsActiveDistributor();
	$distResultSetObj = $distributorMasterObj->fetchAllRecordsActiveDistributor();
	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";	

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/Claim.js"; // For Printing JS in Head SCRIPT section	

	if ($addMode==true) $ON_LOAD_FN = "return HideClaimReturnType();";
	if ($claimType=='MR' && $editMode==true) $ON_LOAD_FN = "return showMaterialReturn();";
	if ($claimType=='FA' && $editMode==true) $ON_LOAD_FN = "return showFixedClaimAmt();";

	if ($editMode) $heading	= $label_editClaim;
	else	       $heading	= $label_addClaim;
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmClaim" action="Claim.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="80%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if ($editMode || $addMode) {
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
									<td colspan="2" >
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
	</tr>
	<tr>
	<? if($editMode){?>
		<td colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Claim.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateClaim(document.frmClaim);">
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Claim.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateClaim(document.frmClaim);"> &nbsp;&nbsp;
		</td>
		<?}?>
	</tr>
	<input type="hidden" name="hidClaimOrderId" value="<?=$editClaimId;?>">
	<tr>
		<td class="fieldName" nowrap >&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" nowrap>
		<table width="200">
                	<tr>
               		         <td class="fieldName">Claim ID : </td>
                                 <td class="listing-item" colspan="2">
					<input name="claimNumber" id="claimNumber" type="text" size="6" <? if ($genClaimId!=0 || $editClaimOrder) { ?> style="border:none" readonly<? }?>  onKeyUp="xajax_chkClaimNumberExist(document.getElementById('claimNumber').value, '<?=$mode?>');" value="<? if($editClaimOrder) { echo  $editClaimOrder;} else if ($genClaimId==1) { echo "New"; } else { echo $p["claimNumber"];}?>">
					<br>
					<div id="divClaimIdExistTxt" style='line-height:normal; font-size:10px; color:red;'></div>
					<input type="hidden" name="hidClaimNumber" value="<?=$editClaimOrder?>">
				</td>				
                                                </tr>
				<tr>
					<TD colspan="2">
						<table>
						<TR><TD class="fieldName">
							<INPUT type="radio" name="claimType" id="claimTypeMR" value="MR" class="chkBox" onclick="showMaterialReturn();" <?=$materialReturnChk?>>Material Return
							<INPUT type="radio" name="claimType" id="claimTypeFA" value="FA" class="chkBox" onclick="showFixedClaimAmt();" <?=$fixedAmtChk?>>Fixed Amt
						</TD></TR>
						</table>
					</TD>
				</tr>
					<? //if($selSOId){?>
                                                    <!--<tr>
                                                      <td class="fieldName" nowrap="nowrap">Distributor</td>
                                                      <td class="listing-item"><?=$distributorName?></td>
                                                    </tr>-->
						<? //}?>					
					<tr>
                                        	<td class="fieldName" nowrap="nowrap">*Date of Settling</td>
                                                      <td nowrap>
							<table width="200" border="0">
                                                          <tr>
                                                            <td>
			<? if($p["lastDate"]!="") $lastDate=$p["lastDate"];?>
				<input type="text" name="lastDate" id="lastDate" size="8" value="<?=$lastDate?>" autocomplete="off" />
				<input type="hidden" name="lastDateStatus" id="lastDateStatus" value="<?=$lastDate?>">
			</td>
                                                            <td><? if($editMode==true){?>
                                                              <table width="100">
                                                                <tr>
                                                                  <td>
									<input name="dateExtended" type="checkbox" id="dateExtended" value="E" onclick="return salesOrderExtendedDateCheck();" class="chkBox" <?=$extendedChk?>>
								</td>
                                                                  <td class="listing-item">Extended</td>
                                                                </tr>
                                                              </table>
                                                            <? }?></td>
                                                          </tr>
                                                        </table></td>
                                                    </tr>
                                              </table>
						</td>
					</tr>

					<!--<tr>
					  <td colspan="2" nowrap class="fieldName" >&nbsp;</td>
					</tr>-->
		<? if (sizeof($salesOrderedItemRecs)>0) {?>
					<!--<tr>
					  <td colspan="2" nowrap>
					<table >
					<TR><TD>
	<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
                                            <tr bgcolor="#f2f2f2" align="center">
						<td width="20">
						<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'selProduct_'); ">
						</td>
                                                <td class="listing-head" style="padding-left:5px;padding-right:5px;">Product</td>
						<td class="listing-head" nowrap style="padding-left:5px;padding-right:5px;">Rate</td>
                                                <td class="listing-head" style="padding-left:5px;padding-right:5px;">Purchased Qty</td>
                                                <td class="listing-head" style="padding-left:5px;padding-right:5px;">Total Amt</td>
						<td class="listing-head" style="padding-left:5px;padding-right:5px;">Defect Qty</td>
						<td class="listing-head" style="padding-left:5px;padding-right:5px;">Type of Defect</td>					
                                                </tr>
		<?
		$totalAmount = 0;
		$m = 0;
		$claimEntryId = "";
		$defectQty  = "";
		$defectType = "";
		foreach ($salesOrderedItemRecs as $sor) {
			$m++;
			$salesOrderEntryId = $sor[0];
			$productId	= $sor[2];
			$unitRate 	= $sor[3];
			$editQuantity	= $sor[4];
			$editTotal	= $sor[5];
			$totalAmount = $totalAmount + $editTotal;
			$productName = $sor[6];
			
			// Edit mode
			$claimEntryId = $sor[7];
			$defectQty	= $sor[8];
			$defectType	= $sor[9];
			$checked = "";
			$selectedProduct = false;
			if ($claimEntryId!="") {
				$checked = "Checked";	
				$selectedProduct = true;
			}

			
		?>
                        <tr bgcolor="#FFFFFF">
				<td width="20" height="25">
					<input type="checkbox" name="selProduct_<?=$m;?>" id="selProduct_<?=$m;?>" value="<?=$productId;?>" <?=$checked?>>
					<input type="hidden" name="hidSelProduct_<?=$m;?>" value="<?=$selectedProduct;?>">
					<input type="hidden" name="salesOrderEntryId_<?=$m;?>" value="<?=$salesOrderEntryId?>">
					<input type="hidden" name="claimEntryId_<?=$m;?>" value="<?=$claimEntryId?>">
				</td>
                               <td class="fieldName" style="padding-left:5px;padding-right:5px;line-height:normal;" nowrap><?=$productName?>
				
				</td>
                                 <td style="padding-left:5px;padding-right:5px;">
					<input name="unitPrice_<?=$m?>" type="text" id="unitPrice_<?=$m?>" value="<?=$unitRate;?>" size="6" style="text-align:right" autoComplete="off" readonly>
				</td>			
                                 <td style="padding-left:5px;padding-right:5px;">
					<input name="quantity_<?=$m?>" type="text" id="quantity_<?=$m?>" size="6" style="text-align:right" value="<?=$editQuantity?>" readonly>
				</td>
                                <td style="padding-left:5px;padding-right:5px;">
					<input name="totalAmount_<?=$m?>" type="text" id="totalAmount_<?=$m?>" size="8" readonly style="text-align:right" value="<?=$editTotal?>">
				</td>
				 <td style="padding-left:5px;padding-right:5px;">
					<input name="defectQty_<?=$m?>" type="text" id="defectQty_<?=$m?>" size="8" style="text-align:right" value="<?=$defectQty?>">
				</td>
				 <td style="padding-left:5px;padding-right:5px;">
					<textarea name="defectType_<?=$m?>" id="defectType_<?=$m?>"><?=$defectType?></textarea>
				</td>
                       </tr>
			<?
				}
			?>
                        <tr bgcolor="#FFFFFF" align="center">
				 <td>&nbsp;</td>
				 <td>&nbsp;</td>
				<td>&nbsp;</td>
                                <td class="listing-head" align="right">Total:</td>
                                                  <td>
							<input name="grandTotalAmt" type="text" id="grandTotalAmt" size="8" style="text-align:right" readonly value="<?=$totalAmount;?>">
						</td>
						 <td>&nbsp;</td>
						<td>&nbsp;</td>
                                                </tr>
                                              </table>
					
						</TD>
						</TR>
						</table>
					
						</td>
						   </tr>-->
					<? }?>						
					<!--<input type="hidden" name="hidItemCount" id="hidItemCount" value="<?=$m;?>">
					<input type="hidden" name="newline" value="">
					<input type="hidden" name="new" value="<?=$m?>">-->
<tr>
	<td colspan="2" nowrap>
		<table>
<!--  Material Return Starts-->
			<TR>
				<TD>
				<div id="materialReturn" style="display:block">
				<table cellpadding="0" cellspacing="0">
				<tr><TD>
				<table>
					<tr>
                                        	<td class="fieldName">*Distributor</td>
                                                <td class="listing-item">
					<select name="selDistributor" id="selDistributor" onchange="xajax_getDistSalesOrder(document.getElementById('selDistributor').value, document.getElementById('startDate').value, document.getElementById('endDate').value, document.getElementById('hidTableRowCount').value);">			
                                        <option value="">-- Select --</option>
					<?	
					while ($dr=$distributorResultSetObj->getRow()) {
						$distributorId	 = $dr[0];
						$distributorCode = stripSlash($dr[1]);
						$distributorName = stripSlash($dr[2]);	
						$selected = "";
						if ($selDistributorId==$distributorId) $selected = "selected";	
					?>
                            		<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
					<? 
						}
					?>
					</select>
					</td>
                                        </tr>
				<tr>
					<td nowrap colspan="2">
					<table cellpadding="0" cellspacing="0">
                      			<tr>
					  	<td class="fieldName">* From:</td>
                                    		<td nowrap="nowrap"> 
                            		           <input type="text" id="startDate" name="startDate" size="8" value="<?=$startDate?>" onchange="xajax_getDistSalesOrder(document.getElementById('selDistributor').value, document.getElementById('startDate').value, document.getElementById('endDate').value, document.getElementById('hidTableRowCount').value);" autoComplete="off"></td>
					    <td class="listing-item">&nbsp;</td>
				            <td class="fieldName">* To:</td>
                                    <td>
                                      <input type="text" id="endDate" name="endDate" size="8"  value="<?=$endDate?>" onchange="xajax_getDistSalesOrder(document.getElementById('selDistributor').value, document.getElementById('startDate').value, document.getElementById('endDate').value,  document.getElementById('hidTableRowCount').value);" autoComplete="off">
					</td>					  
                          </tr>
                    </table>
		</td>
		</tr>
		</table>
		</TD></tr>
				<!--tr>
                                                  <td class="fieldName">*Sales Order</td>
                                                <td class="listing-item">
							<select name="selSalesOrder" id="selSalesOrder" onchange="xajax_getDistSalesOrder('selSalesOrder',document.getElementById('selDistributor').value, document.getElementById('startDate').value, document.getElementById('endDate').value, document.getElementById('selSalesOrder').value);" <?=$disableSO?>>
							<option value="">--Select--</option>
							<?
							/*
							foreach ($salesOrderRecords as $sor) { 
								$salesOrderId	= $sor[0];
								$salesOrderNum	= $sor[1];
								$selected = "";
								if ($selSOId==$salesOrderId || $selSalesOrderId==$salesOrderId) $selected = "Selected";
							*/
							?>
							<<option value="<?=$salesOrderId?>" <?=$selected?>><?=$salesOrderNum?></option>>
							<? //}?>
							</select>
						</td>
                                                </tr-->
<tr><TD>
<table id="tblSalesOrder" cellspacing="1" bgcolor="#999999" cellpadding="3">
	<tr bgcolor="#f2f2f2">
	<td class="listing-head" style="padding-left:5px; padding-right:5px;" align="center">Sales Order</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;" align="center">Items</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;"></td>
	</tr>
</table>
</TD></tr>
<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?//$rowSize;?>">
<tr><TD>
	<a href="###" id='addRow' onclick="javascript:addNewSalesOrder();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
	<input type="hidden" name="grandTotalReturnAmt" id="grandTotalReturnAmt" value="">
</td></tr>
	
				</table>
				</div>
				</TD>
			</TR>
<!--  Material Return Ends-->
<!--  Fixed Amt Starts Here-->
			<TR>
				<TD>
				<div id="fixedClaimAmt" style="display:block">
				<table>
					<tr>
                                        	<td class="fieldName">*Distributor</td>
                                                <td class="listing-item">
					<select name="selFADistributor" id="selFADistributor">			
                                        <option value="">-- Select --</option>
					<?	
					while ($dr=$distResultSetObj->getRow()) {
						$distributorId	 = $dr[0];
						$distributorCode = stripSlash($dr[1]);
						$distributorName = stripSlash($dr[2]);	
						$selected = "";
						if ($selDistributorId==$distributorId) $selected = "selected";	
					?>
                            		<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
					<? 
						}
					?>
					</select>
					</td>
                                        </tr>
					<tr>
						<TD class="fieldName">*Total Amount</TD>
						<td>
							<input type="text" name="toalClaimAmt" id="toalClaimAmt" value="<?=$toalClaimAmt?>" size="6" style="text-align:right;" >
						</td>
					</tr>
					<tr>
						<TD class="fieldName">Debit</TD>
						<td>
							<input type="checkbox" name="debit" id="debit" value="D" <?=$debitChk?> class="chkBox">
						</td>
					</tr>
					<tr>
						<TD class="fieldName">Reason</TD>
						<td>
							<textarea name="fixedAmtReason" id="fixedAmtReason"><?=$fixedAmtReason?></textarea>
						</td>
					</tr>
				</table>
				</div>
				</TD>
			</TR>
<!--  Fixed Amt Ends Here-->
		</table>
	</td>
 </tr>
		
			<tr>
						  <td colspan="2" nowrap class="fieldName" >&nbsp;</td>
					 </tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Claim.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateClaim(document.frmClaim);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Claim.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateClaim(document.frmClaim);">&nbsp;&nbsp;												</td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
												<input type="hidden" name="stockType" value="<?=$stockType?>" />
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
		<?
			}
			
			# Listing Category Starts
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Claim  </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
									<table cellpadding="0" cellspacing="0">
									  <tr>
					<td nowrap="nowrap">
					<table cellpadding="0" cellspacing="0">
                      			<tr>
					  	<td class="listing-item"> From:</td>
                                    		<td nowrap="nowrap"> 
                            		<? 
					if ($dateFrom=="") $dateFrom=date("d/m/Y");
					?>
                            <input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>"></td>
					    <td class="listing-item">&nbsp;</td>
				            <td class="listing-item"> Till:</td>
                                    <td> 
                                      <? 
					   if($dateTill=="") $dateTill=date("d/m/Y");
				      ?>
                                      <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>"></td>
					   <td class="listing-item">&nbsp;</td>
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
                            <td class="listing-item" nowrap >&nbsp;</td>
                          </tr>
                    </table></td></tr></table></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
						<tr>
							<td colspan="3" align="right" style="padding-right:10px;">
<!-- 			here Print section				 -->
								  </td> </tr>
			<tr>
			<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$claimRecordSize;?>);"><?}?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintClaimList.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?></td>
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
									<td width="1" ></td>
									<td colspan="2" >
	<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($claimRecords)>0) {
		$i = 0;
	?>
	<? if($maxpage>1){?>
                <tr  bgcolor="#f2f2f2" align="center">
                <td colspan="7" bgcolor="#FFFFFF" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		$nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"Claim.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Claim.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"Claim.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>
	  </div></td>
       </tr>
	   <? }?>

	<tr  bgcolor="#f2f2f2" align="center">
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Number</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Distributor</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Sales Order Number</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Last Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($claimRecords as $cor) {
		$i++;
		$claimOrderId	= $cor[0];
		$claimNumber	= $cor[1];

		// Find the Total Amount of Each Sales Order
		//$salesOrderTotalAmt = $claimObj->getClaimAmount($claimOrderId);

		$distributorName = $cor[5];
		
		//$salesOrderNo 	= $cor[7];		

		/********************************************************/		
		$selStatusId	= 	$cor[10];

		$currentDate	=	 date("Y-m-d");
		$cDate		=	explode("-",$currentDate);
		$d2 = mktime(22,0,0,$cDate[1],$cDate[2],$cDate[0]);

		$selLastDate	= 	$cor[4]; 	
		$eDate		=	explode("-", $selLastDate);
		$lastDate	=	$eDate[2]."/".$eDate[1]."/".$eDate[0];
		$d1=mktime(0,0,0,$eDate[1],$eDate[2],$eDate[0]);

		$dateDiff = floor(($d2-$d1)/86400);
		$status = "";
		$statusFlag	=	"";
		$extended	=	$cor[6];
		if ($extended=='E' && ($selStatusId=="" || $selStatusId==0)) {
			$status	=	"<span class='err1'>Extended & Pending </span>";
			$statusFlag =	'E';
		} else {
			if ($statusObj->findStatus($selStatusId)) {
				$status	=	$statusObj->findStatus($selStatusId);
			} else if ($dateDiff>0) {
				$status 	= "<span class='err1'>Delayed</span>";
				$statusFlag =	'D';
			} else {
				$status = "Pending";
			}
		}		
		$currentLogStatus	=	$cor[8];
		$currentLogDate		=	$cor[9];
		$dispatchLastDate	=	$cor[4];
		if ((($statusFlag=='E') || ($statusFlag=='D')) && strlen($currentLogStatus)<=1 ) {
			if ($currentLogStatus=='D' && $statusFlag=='E') {
				$statusFlag = $currentLogStatus.",".$statusFlag;
				$dispatchLastDate = $currentLogDate.",".$dispatchLastDate;	
			}
			// Log Status Update
			$logStatusUpted = $claimObj->updateClaimLogStatus($claimOrderId, $statusFlag, $dispatchLastDate);
		}
		/*******************************************************/
		# Get Sales Order numbers
		$getSORecords = $claimObj->getClaimSORecords($claimOrderId);

		$completeStatus = $cor[11];
		$disableEditBtn = "";
		if ($completeStatus=='C') $disableEditBtn = " disabled";

	?>
	<tr  bgcolor="WHITE">
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$claimOrderId;?>" class="chkBox">
			<input type="hidden" name="hidClaimOrderStatus_<?=$i;?>" id="hidClaimOrderStatus_<?=$i;?>" value="<?=$selStatusId?>">
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$claimNumber;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$distributorName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="left">
			<table>
			<tr>
				<?
					$numColumn	=	3;
					if (sizeof($getSORecords)>0) {
						$nextRec	=	0;
						$k=0;
						foreach($getSORecords as $soR) {
							$j++;
							$soNumber=	$soR[2];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$soNumber?>
				</td>
				<? 
					if($nextRec%$numColumn == 0) {
				?>
			</tr>
			<tr>
				<? 
					}	
				}
				}
				?>
			</tr>
			</table>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$lastDate;?></td>
		<td class="listing-item" align="center" nowrap style="padding-left:10px; padding-right:10px;"><?=$status?></td>		
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$claimOrderId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='Claim.php';" <?=$disableEditBtn?>>
		</td>
		<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	
	 <? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
         	<td colspan="7" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"Claim.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Claim.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"Claim.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>
	  </div><input type="hidden" name="pageNo" value="<?=$pageNo?>"></td>
       	 	        </tr>
			<? }?>
	<?
		} else {
	?>
	<tr bgcolor="white">
		<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</table></td>
	</tr>
	<tr>
		<td colspan="3" height="5">
			<input type="hidden" name="hidSelSOId" value="<?=$selSOId?>">
			<input type="hidden" name="editMode" value="<?=$editMode?>">
		</td>
	</tr>
								<tr >
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$claimRecordSize;?>);"><?}?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintClaimList.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
		</td>
		</tr>
		</table></td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>
		
		<tr>
			<td height="10"></td>
		</tr>
<input type='hidden' name='genClaimId' id='genClaimId' value="<?=$genClaimId;?>" >
	</table>
 	<SCRIPT LANGUAGE="JavaScript">
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
	
	<SCRIPT LANGUAGE="JavaScript">
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
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "lastDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "lastDate", 
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
			inputField  : "startDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "startDate", 
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
			inputField  : "endDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "endDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
<? if ($addMode || $editMode) {?>
	<script>
		//multiplyClaimItem();	//Multiply the row
	</script>
<? }?>

	<SCRIPT LANGUAGE="JavaScript">
		function addNewSalesOrder()
		{
			addNewSalesOrderItemRow('tblSalesOrder', '', '<?=$mode?>', '');
			xajax_getDistSalesOrder(document.getElementById('selDistributor').value, document.getElementById('startDate').value, document.getElementById('endDate').value, document.getElementById('hidTableRowCount').value);
		}
	</SCRIPT>
<? if ($addMode) {?>
	<script>
		window.onLoad = addNewSalesOrder();	
	</script>
<? }?>
<? 
	if ($editMode) {
			for ($k=0;$k<sizeof($getSelectedSORecords);$k++) {	
				$gso = $getSelectedSORecords[$k];
				$claimSOEntryId  = $gso[0];	
				$salesOrderId    = $gso[1];
?>
	<script>
		addNewSalesOrderItemRow('tblSalesOrder', <?=$salesOrderId?>, <?=$mode?>, <?=$claimSOEntryId?>);	
		xajax_getSalesOrderItems(<?=$salesOrderId?>,<?=$k?>, <?=$mode?>);
	</script>
<? 
			}
?>
	<script>
	//calcRtQtyAmt();
	</script>
<?	
	 }
?>

	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>