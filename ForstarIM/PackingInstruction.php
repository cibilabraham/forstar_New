<?php
	require("include/include.php");
	require("lib/ChangesUpdateMaster_ajax.php");
	ob_start();
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$selection 	=	"?pageNo=".$p["pageNo"]."&selDistributorFilter=".$p["selDistributorFilter"]."&selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
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
	# Get Refresh time limit
	list($urlFnId, $urlModuleId, $urlSubModuleId) = $modulemanagerObj->getFunctionIds($currentUrl);	
	$rfrshTimeLimit = $refreshTimeLimitObj->getRefreshTimeLimit($urlSubModuleId,$urlFnId);
	$refreshTimeLimit = ($rfrshTimeLimit!=0)?$rfrshTimeLimit:60;	
	/*-----------------------------------------------------------*/

	# Add Category Start 
	if ($p["cmdAddNew"]!="") $addMode  =   true;
	if ($p["cmdCancel"]!="") {
		$addMode   =  false;
		$editMode  = false;

		$cMCMainId = $sessObj->getValue("pkgInstMainId");
		# Update Rec
		if ($cMCMainId!=0) {
			$updateModifiedRec = $packingInstructionObj->updatePkgInstEditingRec($cMCMainId, '', 'U');
			$sessObj->updateSession("pkgInstMainId",0);
		}	
	}

	if ($p["selSOId"]!="")	$selSOId = $p["selSOId"];


	# Add a Record
	/*
	if ($p["cmdAdd"]!="") {	
		$selDistributor = $p["selDistributor"];
		$selProduct	= $p["selProduct"];
		$indexNo	= $p["indexNo"];

		# check Product Identifier exist
		$productIdentifierExist = $packingInstructionObj->chkProductIdentifierExist($selDistributor, $selProduct, $cId);

		if ($selDistributor!="" && $selProduct!="" && $indexNo!="" && !$productIdentifierExist) {
			$pkngInstRecIns = $packingInstructionObj->addProductIdentifier($selDistributor, $selProduct, $indexNo, $userId);
			if ($pkngInstRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddPackingInstruction);
				$sessObj->createSession("nextPage",$url_afterAddPackingInstruction.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddPackingInstruction;
			}
			$pkngInstRecIns = false;
		} else {
			$addMode = true;
			if ($productIdentifierExist) $err = $msg_failAddPackingInstruction."<br/>The selected records existing in our database.";
			else $err = $msg_failAddPackingInstruction;
		}
	}
	*/

	# Update a Record
	if ($p["cmdSaveChange"]!="" || $p["cmdSaveConfirm"]!="") {
		
		$pkngInstructionId 	= $p["hidPkngInstId"];

		$hidProductRowCount	= $p["hidProductRowCount"];	// Product Row Count
		$hidMCActRowCount	= $p["hidMCActRowCount"];	// MC Actual Row Count
		$hidItemTbleRowCount	= $p["hidItemTbleRowCount"];	// Additional Item Row Count

		$mcTotalActualWt	= $p["mcTotalActualWt"];
		$additionalItemTotalWt	= $p["additionalItemTotalWt"];	
		$totalGrossWt		= $p["totalGrossWt"];
		$mcDoneBy		= $p["mcDoneBy"];
		$verifiedBy		= $p["verifiedBy"];

		if ($p["cmdSaveConfirm"]!="") $packingConfirm = 'C';
		else $packingConfirm = 'P';

		//$packingConfirm		= $p["packingConfirm"];
		$prdMCTotalActualWt	= $p["prdMCTotalActualWt"];

		$selSOId		= $p["hidSelSOId"];
		$alreadyConfirmed = $p["alreadyConfirmed"];
		$canUpdate = false;
		if ($alreadyConfirmed=="" || $isAdmin==true || $reEdit==true) {
			$canUpdate = true;
		}	

		if ($pkngInstructionId!="" && $canUpdate) {
			# Update main Table
			$pkngInstRecUptd = $packingInstructionObj->updatePackingInstruction($pkngInstructionId, $mcTotalActualWt, $additionalItemTotalWt, $totalGrossWt, $mcDoneBy, $verifiedBy, $packingConfirm, $prdMCTotalActualWt);
			if ($pkngInstRecUptd) {
				# Delete All Entry Rec
				$delPkngInstEntyRec = $packingInstructionObj->delPkngInstAllMainEntryRec($pkngInstructionId);

				# Delete from t_pkng_inst_prd
				//$delPkngInstPrdRec = $packingInstructionObj->delPkngInstPrdRec($pkngInstructionId);
				# Delete From t_pkng_inst_mc_wt
				//$delPkngInstMCWt = $packingInstructionObj->delPkngInstMCWtRec($pkngInstructionId);	
			}

			for ($i=0; $i<$hidProductRowCount; $i++) {

				$hidPrBtchNoRowCount 	= $p["hidPrBtchNoRowCount_".$i];
				$hidPkngDtlsRowCount 	= $p["hidPkngDtlsRowCount_".$i];
				$hidMCActualWtRowCount	= $p["hidMCActualWtRowCount_".$i];

				$selProduct 	= $p["selProduct_".$i];
				$remarks	= $p["remarks_".$i];
				$sPkgInstPrdEId  = $p["pkgInstPrdEId_".$i]; // When edit Mode

				if ($sPkgInstPrdEId!="") {
					$delPkngInstProductEntryRecs = $packingInstructionObj->delAllPkngInstPrdEntryRecs($sPkgInstPrdEId);
				}

				
				if ($selProduct) {
					$pkgPrdInstEntry = $packingInstructionObj->addPkngInstProduct($pkngInstructionId, $selProduct, $remarks);
					if ($pkgPrdInstEntry) {
						#Find the Last inserted Id From t_pkng_inst_prd Table
						$pkgInstPrdEId = $databaseConnect->getLastInsertedId();
						if ($pkgInstPrdEId) {
							# Add Product Wise Actual Gross Wt
							for ($mc=0; $mc<$hidMCActualWtRowCount; $mc++) {
								$mcGrossWt = trim($p["mcActualGrossWt_".$i."_".$mc]);
								$rowId =  $p["rowId_".$i."_".$mc];
						    		if ($mcGrossWt!=0) {
									$addPrdMCWt = $packingInstructionObj->addPrdMCActualGrossWt($pkgInstPrdEId, $mcGrossWt, $rowId);
								}
							} // MC Ends Here	

							# Add Product batch No		
							for ($j=0; $j<$hidPrBtchNoRowCount; $j++) {
								$btchRowStatus 	= $p["btchRowStatus_".$i."_".$j];
								$productBatchNo =  trim($p["productBatchNo_".$i."_".$j]);
						    		if ($btchRowStatus!='N' && $productBatchNo) {
									$addPkngInstProductBtch = $packingInstructionObj->addProductBatchNo($pkgInstPrdEId, $productBatchNo);
								}
							} // Product Batch No Ends Here
							# Add Pkng Details
							for ($k=0; $k<$hidPkngDtlsRowCount; $k++) {
								$pkngDtlsRowStatus 	= $p["pkngDtlsRowstatus_".$i."_".$k];
								$pkngMaterialBatchNo = trim($p["pkngMaterialBatchNo_".$i."_".$k]);
								$pkngMaterialName =  trim($p["pkngMaterialName_".$i."_".$k]);
								$pkngQtyUsed =  trim($p["pkngQtyUsed_".$i."_".$k]);
	
						    		if ($pkngDtlsRowStatus!='N' && $pkngQtyUsed) {
									$addPkngDetails = $packingInstructionObj->addPrdPkngDtls($pkgInstPrdEId, $pkngMaterialBatchNo, $pkngMaterialName, $pkngQtyUsed);
								}
							} // Product Pkng Details Ends Here
						} // Entry Id Loop Ends Here
					}
				}
			} // Product Row Ends Here

			# MC Actual Wt
			for ($l=0; $l<$hidMCActRowCount; $l++) {
				$mcPackId 		= $p["mcPackId_".$l];
				$mcActualGrossWt	= $p["mcActualGrossWt_".$l];
				$rowId			= $p["rowId_".$l];
				$mcProductId		= $p["mcProductId_".$l];				

				if ($mcActualGrossWt) {
					$addMCActualGrossWt = $packingInstructionObj->addMCActualGrossWt($pkngInstructionId, $mcPackId, $mcActualGrossWt, $mcProductId, $rowId);
				}
			}
			
			# Additional Item
			for ($m=0; $m<$hidItemTbleRowCount; $m++) {
				$status = $p["status_".$m];
				if ($status!='N') {
					$itemName	= trim($p["itemName_".$m]);
					$itemWt		= trim($p["itemWt_".$m]);
					if ($pkngInstructionId!="" && $itemName!="" && $itemWt!="") {
						$adnlItemRecIns = $packingInstructionObj->addAdnlItem($pkngInstructionId, $itemName, $itemWt);
					}	
				}
			} // Additional Item Ends Here	
			
		} // Main Condition ends here	

		# Update Sales Order 
		if ($packingConfirm=='C' && $canUpdate) {
			$updateSalesOrderRec = $packingInstructionObj->updateSOPkngInstRec($selSOId, "Y");	
		} else if ($alreadyConfirmed!="" && $packingConfirm!='C') {
			$updateSalesOrderRec = $packingInstructionObj->updateSOPkngInstRec($selSOId, "N");
		}

		if ($pkngInstRecUptd) {
			if ($pkngInstructionId!="") {
				$updateModifiedRec = $packingInstructionObj->updatePkgInstEditingRec($pkngInstructionId, '', 'U');
				$sessObj->updateSession("pkgInstMainId",0);
			}
			$sessObj->createSession("displayMsg",$msg_succPackingInstructionUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePackingInstruction.$selection);
		} else {
			$editMode	=	true;
			if  ($productIdentifierExist) $err = $msg_failPackingInstructionUpdate."<br/>The selected records existing in our database.";
			else $err = $msg_failPackingInstructionUpdate;
		}
		$pkngInstRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$pkngInstRec		= $packingInstructionObj->find($editId);

		# Chk already modified
		$selUsername = $packingInstructionObj->chkPkgInstModified($editId);	
		if ($selUsername) {
			$err	= "<b>$selUsername has been editing this record.</b>";	
			$editMode = false;
			$editId = "";	
			$editPkngInstId = "";			
		}

		$editPkngInstId 	= $pkngInstRec[0];
		$sessObj->createSession("pkgInstMainId",$editPkngInstId);
		# Update Rec
		if ($editPkngInstId) $updateModifiedRec = $packingInstructionObj->updatePkgInstEditingRec($editPkngInstId, $userId, 'E');

		$selSOId		= $pkngInstRec[1];	

		$invType = $pkngInstRec[2];
		$soNo 	= $pkngInstRec[3];
		$pfNo 	= $pkngInstRec[4];
		$saNo	= $pkngInstRec[5];				
		$sInvoiceNo = "";
		if ($soNo!=0) $sInvoiceNo=$soNo;
		else if ($invType=='T') $sInvoiceNo = "P$pfNo";
		else if ($invType=='S') $sInvoiceNo = "S$saNo";	

		$mcTotalActualWt	= $pkngInstRec[7];
		$additionalItemTotalWt	= $pkngInstRec[8];
	
		$totalGrossWt		= $pkngInstRec[9];
		$mcDoneBy		=  $pkngInstRec[10];
		$verifiedBy		= $pkngInstRec[11];
		$packingConfirmChk	= ($pkngInstRec[12]=='C')?"checked":"";	
		$prdMCTotalActualWt	= $pkngInstRec[13];

		# Get Additional Item Recs
		$adnlItemRecs	= $packingInstructionObj->getAdnlItemRecs($editPkngInstId, $selSOId);
		/*
		echo "<pre>";
		print_r($adnlItemRecs);
		echo "</pre>";
		*/
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$pkngInstructionId	= $p["delId_".$i];
			$pknginstStatus		= $p["hidPknginstStatus_".$i];
			$soId			= $p["hidSOId_".$i];

			if ($pkngInstructionId!="" && $pknginstStatus!='C') {
				
				$pkngInstRecDel = $packingInstructionObj->deletePackingInstruction($pkngInstructionId);
				# update So
				if ($pkngInstRecDel) {
					$updateSOMainRec = $packingInstructionObj->uptdSOPkngRec($soId);
				}
			}
		}
		if ($pkngInstRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPackingInstruction);
			$sessObj->createSession("nextPage",$url_afterDelPackingInstruction.$selection);
		} else {
			$errDel	=	$msg_failDelPackingInstruction;
		}
		$pkngInstRecDel	=	false;
	}
	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "") $pageNo=$g["pageNo"];
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
		$dateC	   =	explode("/", date("d/m/Y"));
		$dateFrom   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],1,$dateC[2]));
		$dateTill   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1], date('t'), $dateC[2]));	
	}


	# List all Packing Instruction
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$packingInstructionRecords = $packingInstructionObj->fetchAllPagingRecords($offset, $limit, $fromDate, $tillDate);

		$packingInstructionRecordSize	= sizeof($packingInstructionRecords);

		$fetchAllRecs = $packingInstructionObj->fetchAllRecords($fromDate, $tillDate);
	}

	

	## -------------- Pagination Settings II -------------------	
	$numrows	=  sizeof($fetchAllRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	#List all Distributor	
	//$distributorFilterResultSetObj = $distributorMasterObj->fetchAllRecords();

	if ($addMode || $editMode) {
		//$soRecords = $packingInstructionObj->getSORecords();
		//$selSOId = 66;
		if ($selSOId) {

			# List all ordered items
			$salesOrderEntryRecs = $packingInstructionObj->salesOrderEntryRecs($selSOId);

			$sORec = $packingInstructionObj->getSORecord($selSOId);
			$distributorName = $sORec[6];	
			$distributorId	 = $sORec[2];	
			$createDate	 = $sORec[3];	
			$salesOrderNo	 = $sORec[1];	
			$selStatusId	 = $sORec[7];	
			$selPaymentStatus   = $sORec[8];
			$lastDate	 = $sORec[20]; //dateFormat($lastDate)
			$selDispatchDate = ($sORec[9]!="")?dateFormat($sORec[9]):dateFormat($lastDate);
			$grossWt	 = $sORec[10];
			$invoiceType	= $sORec[18]; // T ->Taxable: S->Sample
			if ($invoiceType=='S') {
				//$additionalItemTotalWt = $sORec[19];
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
			$productMRPRateListId	= $sORec[37];
			$distMgnRateListId	= $sORec[38];	
			$stateId		= $sORec[39];
			//echo "$productMRPRateListId:$distMgnRateListId";

		} // Chk SO Selected Ends Here
	}

	#heading Section
	if ($editMode) $heading	= $label_editPackingInstruction;
	else	       $heading	= $label_addPackingInstruction;

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	

	$ON_LOAD_PRINT_JS	= "libjs/PackingInstruction.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmPackingInstruction" id="frmPackingInstruction" action="PackingInstruction.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="75%" >
	<tr><TD height="10">
		<input type="hidden" name="hidSelSOId" value="<?=$selSOId?>">
	</TD></tr>	
		<?
			if ($editMode) {
		?>
		<tr><TD height="5"></TD></tr>
		<tr>
			<td align="center" id="timeTickerRow" class="err1" height="20" style="font-size:14pt;" onMouseover="ShowTip('Time remaining to cancel the selected record.');" onMouseout="UnTip();">	
			</td>			
		</tr>
		<?
			}
		?>	
		<?php
		if (sizeof($packingInstructionRecords)>0) {
		?>	
			<tr>
				<td align="center" id="refreshMsgRow" class="err1" style="font-size:9pt;line-height:20px;">	
				</td>			
			</tr>
		<?php
			}
		?>
	<tr>
		<td height="10" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
		<td></tr>
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
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
											<!--<tr>
												<? if($editMode){?>

											  <td colspan="2" align="center">
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('PackingInstruction.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validatePackingInstruction(document.frmPackingInstruction);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingInstruction.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePackingInstruction(document.frmPackingInstruction);">												</td>

												<?}?>
											</tr>-->
		<input type="hidden" name="hidPkngInstId" value="<?=$editPkngInstId;?>">
											<tr>
	<td colspan="2" nowrap>
	<table width="200">
	<tr>
		<TD colspan="2">
			<table>
				<tr>
				<td class="fieldName" nowrap >*Invoice No:</td>
				<TD class="listing-item"><?=$sInvoiceNo?></TD>
				<td nowrap="true">
					<table>
					<tr>
						<TD>
							<table>
								<tr>
									<td class="fieldName" nowrap="nowrap">Distributor:</td>
									<td class="listing-item" nowrap="true" align="left">&nbsp;<?=$distributorName?></td>
									<td class="fieldName" nowrap="nowrap">Despatch Date:</td>
									<td class="listing-item" nowrap="true" align="left">&nbsp;<?=$selDispatchDate?></td>
								</tr>
							</table>
						</TD>
					</tr>
							
						</table>
				</td>
			</tr>
			</table>
		</TD>
	</tr>
	
	<?php
		if ($selSOId) {
	?>
	<!--<tr>
	<TD colspan="2">
		<table>
		<tr>
			<TD>
				<table>
					<tr>
                                    		 <td class="fieldName" nowrap="nowrap">Distributor:</td>
                                                 <td class="listing-item" nowrap="true" align="left">&nbsp;<?=$distributorName?></td>
						 <td class="fieldName" nowrap="nowrap">Despatch Date:</td>
						<td class="listing-item" nowrap="true" align="left">&nbsp;<?=$selDispatchDate?></td>
                                         </tr>
				</table>
			</TD>
		</tr>
				
			</table>
		</TD>
	</tr>-->
	<tr>
		<TD colspan="2" style="padding-left:5px;padding-right:5px;">
			<table cellpadding="1"  width="65%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
	$m = 0;
	if (sizeof($salesOrderEntryRecs)>0) {		
	?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:3px; padding-right:3px; line-height:normal;" rowspan="2">SR.<br>NO</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;" rowspan="2">DESCRIPTION OF GOODS</td>
		<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">MRP</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">TOTAL<br/>PKTS</td>		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">M/C Pkg</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">M/C</td>-->
		<td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;" rowspan="2" nowrap>MC<br> ACTUAL WT</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;" rowspan="2">BATCH NO<br>(PRODUCT)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;" colspan="3">PACKING MATERIAL USED</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;" rowspan="2">REMARKS</td>
	</tr>
	<tr bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;">BATCH NO</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;">MATERIAL NAME</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;">QTY</td>
	</tr>
	<?php
	$totalAmount = 0;
	$totalNumMCPack = 0;
	$totalNumLoosePack = 0;
	$totalQuantity = 0;
	$totalFreePkts = 0;
	$mcArray = array();
	$mc=0;
	foreach ($salesOrderEntryRecs as $sor) {
				
		$prodRate	= $sor[3];
		$prodQty	= $sor[4];
		$totalQuantity  += $prodQty;
		$prodTotalAmt 	= $sor[5];
		$totalAmount 	+= $prodTotalAmt;
		$selProductId	= $sor[2];
		$productName	= "";
		$productRec	= $manageProductObj->find($selProductId);
		$productName	= $productRec[2];				
		$numMCPack	= $sor[7];
		$totalNumMCPack += $numMCPack;
		$numLoosePack	= $sor[8];
		$totalNumLoosePack += $numLoosePack;
		$freePkts = $sor[13];
		$totalFreePkts += $freePkts;
		$mcPakingId	= $sor[6];

		# Find MC Packs Details	---------------------------
			$mcpackingRec	= $mcpackingObj->find($mcPakingId);
			$casePack	= $mcpackingRec[2];
		# Product MRP$pkngInstRecUptd
		$productMRP = $orderDispatchedObj->getProductMRP($selProductId, $productMRPRateListId, $distributorId, $stateId);
		
		$productRec	= $manageProductObj->find($selProductId);
		$productNetWt	= $productRec[6];
		# Find Mc Wt
		$mcPkgWtId = $sor[17];
		list($mcPackageWt, $pkgWtTolerance) = $packingInstructionObj->getMCPkgWt($mcPakingId, $productNetWt, $mcPkgWtId);

		//$sCasePack = ($numMCPack!=0)?$casePack:1;
		$nMCPack = ($numMCPack!=0)?$numMCPack:1;
		if ($numLoosePack!=0) {
			for ($j=0;$j<$nMCPack;$j++) {				
				$mcArray[$mcPakingId] =  array($mcPakingId, $casePack, $selProductId, $mcPackageWt);
				$mc++;
			}
		}
		
		$pkgInstPrdEId  = $sor[15];
		$remarks	= $sor[16];
		if ($pkgInstPrdEId) {
			$getPrdBatchNoRecs 	= $packingInstructionObj->getProductBtchRecs($pkgInstPrdEId);
			$getPrdPkngDtlsRecs 	= $packingInstructionObj->getProductPkngDtlsRecs($pkgInstPrdEId);
		}

		$noOfPCs	= $numMCPack*$prodQty;
		
	?>	
	<tr bgcolor="WHITE">
		<td height="20" align="center" class="listing-item" style="padding-left:3px; padding-right:3px;" nowrap="true" valign="top">
			<?=$m+1?>
			<input type="hidden" name="pkgInstPrdEId_<?=$m?>" id="pkgInstPrdEId_<?=$m?>" value="<?=$pkgInstPrdEId?>">
			<input type="hidden" name="selMCPackageWt_<?=$m?>" id="selMCPackageWt_<?=$m?>" value="<?=$mcPackageWt?>">
			<input type="hidden" name="selPkgWtTolerance_<?=$m?>" id="selPkgWtTolerance_<?=$m?>" value="<?=$pkgWtTolerance?>">
		</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap="true" valign="top">		
			<input type="hidden" name="selProduct_<?=$m?>" id="selProduct_<?=$m?>" value="<?=$selProductId?>">
			<table>
				<TR><TD class="listing-item" nowrap><?=$productName?></TD></TR>
				<tr>
					<TD>
						<table>
						<tr>
							<TD valign="top">
							<table>
							<TR>
								<TD class="listing-head" nowrap>MRP :</TD>
								<TD class="listing-item" nowrap><strong><?=$productMRP?></strong></TD>
							</TR>
							</table>
							</TD>
							<TD valign="top">
							<table><TR>
								<TD class="listing-head" nowrap>NO. OF PKTS :</TD>
								<TD class="listing-item" nowrap><strong><?=$prodQty?></strong></TD>
							</TR></table>
							</TD>
						</tr>
						<tr>
							<TD valign="top">
							<table>
							<TR>
								<TD class="listing-head" nowrap>PKTS/MC :</TD>
								<TD class="listing-item" nowrap><strong><?=$casePack?></strong></TD>
							</TR>
							</table>
							</TD>
							<TD valign="top">
							<table>
							<TR>
								<TD class="listing-head" nowrap>NO. OF MC :</TD>
								<TD class="listing-item" nowrap><strong><?=$numMCPack?></strong></TD>
							</TR>
							</table>
							</TD>
						</tr>					
						<!--<TR>
							<TD class="listing-head">PACKING:</TD>
							<TD>
								<table>
								<TR>
									<TD>
									<table>					
									<TR>
									<td class="listing-item">
										<?=$noOfPCs;?>
									</td>
									<TD class="listing-head" nowrap="true">PCS</TR>
									</TR>
									</table>			  
									</TD>
									<td>=</td>
									<TD>
										<table>
										<TR>
										<TD class="listing-item">
										<?=$prodQty?>		
										</TD>
										<TD class="listing-head">PKTS</TD>	
										<td class="listing-item">&nbsp;X&nbsp;<?=$numMCPack?>&nbsp;</td>	
										<td class="listing-head" nowrap="true">MCs</td>
										</TR>
										</table>		 
									</TD>
								</TR>
								</table>
							</TD>
						</TR>-->
						<TR>							
							<TD colspan="2">
							<table><TR><TD class="listing-head" nowrap>Gross Wt per MC =</TD>
							<td class="listing-item" nowrap><strong><?=$mcPackageWt?>&nbsp;Kg</strong> </td>
							</TR></table>
							</TD>
						</TR>
						</table>
					</TD>
				</tr>
			</table>
		</td>	
		<!--<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right" valign="top"><?=$productMRP?></td>		
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right" valign="top">	
			<?=number_format($prodQty,0,'.','');?>		
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right" valign="top">	
			<?="X&nbsp;".$casePack?>		
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right" valign="top"><?=$numMCPack?></td>-->
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right" valign="top">
			<table>
				<?php
					$mcPrdActualGrossWt = "";
					for ($b=0; $b<$numMCPack; $b++) {
						if ($pkgInstPrdEId) {
							$mcPrdActualGrossWt = $packingInstructionObj->prdWiseActualGrossWt($pkgInstPrdEId, $b);

						}		

					
				?>
					<TR>
						<TD class="listing-item"><?=$b+1?>.</TD>
						<TD class="listing-item" >
							<input type="text" name="mcActualGrossWt_<?=$m?>_<?=$b?>" id="mcActualGrossWt_<?=$m?>_<?=$b?>" size="6" style="text-align:right;" value="<?=$mcPrdActualGrossWt?>" onkeyup="calcPrdWiseTotalGrossWt(); chkActualWtVariation('<?=$m?>')" onkeypress="return nextTBox(event,'document.frmPackingInstruction','mcActualGrossWt_<?=$m?>_<?=$b+1?>');" autocomplete="off" />
							<input type="hidden" name="rowId_<?=$m?>_<?=$b?>" id="rowId_<?=$m?>_<?=$b?>" value="<?=$b?>">
							<input type="hidden" name="mcPackageWt_<?=$m?>_<?=$b?>" id="mcPackageWt_<?=$m?>_<?=$b?>" value="<?=$mcPackageWt?>">
							<input type="hidden" name="pkgWtTolerance_<?=$m?>_<?=$b?>" id="pkgWtTolerance_<?=$m?>_<?=$b?>" value="<?=$pkgWtTolerance?>">				
						</TD>
					</TR>
				<? }?>
				<input type="hidden" name="hidMCActualWtRowCount_<?=$m?>" id="hidMCActualWtRowCount_<?=$m?>" value="<?=$b?>">
			</table>
		</td>		
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center" valign="top">
			<table><TR><TD>
			<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblPrdBtchNo_<?=$m?>">
			</table>
			<input type="hidden" name="hidPrBtchNoRowCount_<?=$m?>" id="hidPrBtchNoRowCount_<?=$m?>" value="">
			</td></tr>
			<tr>
				<TD style="padding-left:5px;padding-right:5px;" nowrap="true">
					<a href="###" id='addRow' onclick="javascript:addNewPrdBtchNo('tblPrdBtchNo_', '<?=$m?>', document.getElementById('hidPrBtchNoRowCount_<?=$m?>').value, '');"  class="link1" title="Click here to add new Batch No."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
				</TD>
			</tr>
			</table>
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center" colspan="3" valign="top">
			<table><TR><TD>
			<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblPkngDtls_<?=$m?>">
			</table>
			<input type="hidden" name="hidPkngDtlsRowCount_<?=$m?>" id="hidPkngDtlsRowCount_<?=$m?>" value="">
			</td></tr>
			<tr>
				<TD style="padding-left:5px;padding-right:5px;">
					<a href="###" id='addRow' onclick="javascript:addNewPkngDtls('tblPkngDtls_', '<?=$m?>', document.getElementById('hidPkngDtlsRowCount_<?=$m?>').value, '', '', '');"  class="link1" title="Click here to add new Batch No."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
				</TD>
			</tr>
			</table>
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center" valign="top">	
			<textarea name="remarks_<?=$m?>" id="remarks_<?=$m?>" rows="2"><?=$remarks?></textarea>
		</td>
	</tr>
<!--  Product Btch No	 -->
	<? if (sizeof($getPrdBatchNoRecs)>0) {?>
		<script language="JavaScript">
		<?php 
		$pBtchSubRowId	= 0;
		foreach ($getPrdBatchNoRecs as $gpb) {
			$pBtchNo = $gpb[1];
		?>
			addNewPrdBtchNo('tblPrdBtchNo_', '<?=$m?>','<?=$pBtchSubRowId?>', '<?=$pBtchNo?>');
		<?php 	
			$pBtchSubRowId++;
			}
		?>
		</script>
	<? } else {?>
		<script language="JavaScript">
			addNewPrdBtchNo('tblPrdBtchNo_', '<?=$m?>', 0, '');
		</script>
	<? }?>
<!--  Packing details -->
	<? if (sizeof($getPrdPkngDtlsRecs)>0) {?>
		<script language="JavaScript">
		<?php 
		$pkngSubRowId	= 0;
		foreach ($getPrdPkngDtlsRecs as $gpbd) {
			$pkngBtchNo 	= $gpbd[1];
			$pkngMatName	= $gpbd[2];
			$pkngQty	= $gpbd[3];
		?>			
			addNewPkngDtls('tblPkngDtls_', '<?=$m?>','<?=$pkngSubRowId?>', '<?=$pkngBtchNo?>', '<?=$pkngMatName?>', '<?=$pkngQty?>');
		<?php 	
			$pkngSubRowId++;
			}
		?>
		</script>
	<? } else {?>
		<script language="JavaScript">
			addNewPkngDtls('tblPkngDtls_', '<?=$m?>', 0, '', '', '');
		</script>
	<? }?>
	<?php
		$m++;
		}
		/*
		echo "<pre>";
		print_r($mcArray);
		echo "</pre>";
		*/
	?>					
	<tr bgcolor="white">
		<td class="listing-head" align="right" colspan="2">
		<table>				
		<tr>
			<TD>
			<table>
				<TR>
					<TD class="listing-head" nowrap>GRAND TOTAL:</TD>
					<TD>
					<table>
					<TR>
					<TD valign="top">
					<table>
									<TR><TD class="listing-head" nowrap>PKTS = </TD>
									<td class="listing-item" nowrap>
										<strong><?=$totalQuantity?></strong>
									</td>
									</TR>
									</table>			  
									</TD>
									<td>&nbsp;</td>
									<TD valign="top">
										<table>
										<TR><TD class="listing-head" nowrap="true">MC = </TD>
										<TD class="listing-item">
										<strong>
										<?=$totalNumMCPack?>
										</strong>
										</TD>				
										</TR>
										</table>		 
									</TD>
								</TR>
								</table>
							</TD>
						</TR>
						</table>
					</TD>
				</tr>
			</table>
		</td>		
		<!--<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><?=$totalQuantity?></strong></td>		
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><?=$totalNumMCPack?></strong></td>-->
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;">
			<input type='text' name="prdMCTotalActualWt" id="prdMCTotalActualWt" value="<?=$prdMCTotalActualWt?>" size="6" readonly="true" style="border:none;text-align:right;font-weight:bold;">
		</td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;" colspan="3"></td>		
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"></td>
	</tr>		
	<?php 
		} else { 
	?>
	<tr bgcolor="white">
		<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?php
		}
	?>
	<input type="hidden" name="hidProductRowCount" id="hidProductRowCount" value="<?=$m?>" >
	</table>
	</TD>
	</tr>
	<tr>
		<TD colspan="2" style="padding-left:5px;padding-right:5px;" valign="top">
		<table>
			<TR>
			<TD valign="top">
				<fieldset style="padding:5 5 5 5px">
				<legend class="listing-item">MC Gr. Wt - Loose Packs<!--MC Actual Gross Wt(LP)--></legend>				
				<table cellpadding="1"  width="65%" cellspacing="1" border="0" align="center" bgcolor="#999999">
				<?php
					if (sizeof($mcArray)>0) {
				?>
					<TR bgcolor="#f2f2f2" align="center">
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Sl.No</TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">MC Pkg</TD>	
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Wt (Kg)</TD>
					</TR>
					<?php 						
						$kId = 0;
						$mcPackageWt = "";						
						foreach ($mcArray as $mca) {
							$mcPackId 	= $mca[0];
							$numCasePack	= $mca[1];
							$mcProductId	= $mca[2];
							$mcPackageWt	= $mca[3];

							if ($editPkngInstId) {
								$mcActualGrossWt = $packingInstructionObj->getMcActualGrossWt($editPkngInstId, $mcPackId, $mcProductId, $kId);
							}
					?>
					<TR bgcolor="WHITE">
						<TD class="listing-item" align="center" style="padding-left:5px; padding-right:5px;" height="20"><?=$kId+1?></TD>
						<TD class="listing-item" align="center" style="padding-left:5px; padding-right:5px;" height="20">X&nbsp;<?=$numCasePack?></TD>	
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;">
							<input type="hidden" name="rowId_<?=$kId?>" id="rowId_<?=$kId?>" value="<?=$kId?>">
							<input type="hidden" name="mcProductId_<?=$kId?>" id="mcProductId_<?=$kId?>" value="<?=$mcProductId?>">
							<input type="hidden" name="mcPackId_<?=$kId?>" id="mcPackId_<?=$kId?>" value="<?=$mcPackId?>">
							<input type="hidden" name="mcPackageWt_<?=$kId?>" id="mcPackageWt_<?=$kId?>" value="<?=$mcPackageWt?>">
							<input type="text" name="mcActualGrossWt_<?=$kId?>" id="mcActualGrossWt_<?=$kId?>" size="6" style="text-align:right;" value="<?=$mcActualGrossWt?>" onkeyup="calcMCActualGWt();" autocomplete="off" />
							
						</TD>
					</TR>
					<?php 
						$kId++;
						} // MC Actual Gross Wt
					} // Size check
					?>
	<input type="hidden" name="hidMCActRowCount" id="hidMCActRowCount" value="<?=$kId?>">
					<TR bgcolor="white" align="center">
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;" colspan="2">Total</TD>	
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;">
							<input type='text' name="mcTotalActualWt" id="mcTotalActualWt" value="<?=(sizeof($mcArray)>0)?$mcTotalActualWt:0;?>" size="6" readonly="true" style="border:none;text-align:right;font-weight:bold;">
						</TD>
					</TR>		
				</table>
				
				</fieldset>
			</TD>
	<TD style="padding-left:5px;padding-right:5px;" valign="top">
		<table>
			<TR>
			<TD>
				<fieldset>
				<legend class="listing-item">Additional Item</legend>
				<table><TR><TD>
				<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblSOAdditionalItem">
		                <tr bgcolor="#f2f2f2" align="center">
					<td class="listing-head">Item</td>
					<td class="listing-head">Wt (Kg)</td>
					<td></td>
                        	</tr>
				<tr bgcolor="white">
					<td class="listing-head" style="padding-left:5px;padding-right:5px;">Total Wt:</td>
					<td class="listing-head">
						<input type='text' name="additionalItemTotalWt" id="additionalItemTotalWt" value="<?=$additionalItemTotalWt?>" size="6" readonly="true" style="border:none;text-align:right;font-weight:bold;">
					</td>
					<td></td>
                        	</tr>
				</table>
	<input type='hidden' name="hidItemTbleRowCount" id="hidItemTbleRowCount" value="">	
				</TD>
				</TR>
				<tr><TD height="5"></TD></tr>
<tr>
	<TD style="padding-left:10px;padding-right:10px;">
		<a href="###" id='addRow' onclick="javascript:addNewAdditionalItem();"  class="link1" title="Click here to add new additional item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add additional Item</a>
	</TD>
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
		<TD valign="top">
		<fieldset>
		<!--<legend class="listing-item"></legend>-->
			<table>
			<TR>
				<TD class="fieldName">Total Gross Wt:</TD>
				<td>
					<input type="text" name="totalGrossWt" id="totalGrossWt" value="<?=$totalGrossWt?>" size="8" style="border:none;text-align:right;font-weight:bold;">
				</td>
			</TR>
			<TR>
				<TD class="fieldName">MC Done By:</TD>
				<td>
					<input type="text" name="mcDoneBy" id="mcDoneBy" value="<?=$mcDoneBy?>" size="12" onkeyup="enbleCfmBtn();">	
				</td>
			</TR>
			<TR>
				<TD class="fieldName">Verified By:</TD>
				<td>
					<input type="text" name="verifiedBy" id="verifiedBy" value="<?=$verifiedBy?>" size="12" onkeyup="enbleCfmBtn();">	
				</td>
			</TR>
			<TR style="display:none;">
				<TD class="fieldName">Confirm</TD>
				<td>
					<input type="hidden" name="packingConfirm" id="packingConfirm" class="chkBox" value="C" <?=$packingConfirmChk?>>
					<input type="hidden" name="alreadyConfirmed" id="alreadyConfirmed" value="<? if($packingConfirmChk) echo 'Y';?>">	
				</td>
			</TR>			
			</table>
		</fieldset>
		</TD>
		</TR>
		</table>
	</td>
			<TD><!--Another column--></TD>
			</TR>
		</table>
		</TD>
	</tr>
	<?php
		}
	?>
         </table>
	</td>
				  </tr>
					<tr>
							<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingInstruction.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePackingInstruction(document.frmPackingInstruction, false);">	
&nbsp;&nbsp;
		<input type="submit" name="cmdSaveConfirm" id="cmdSaveConfirm" class="button" value=" Save & Confirm " onClick="return validatePackingInstruction(document.frmPackingInstruction, true);">	
											</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingInstruction.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePackingInstruction(document.frmPackingInstruction, true);">												</td>
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
		<?
			}
			
			# Listing Category Starts
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
	<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
			<tr>
		<td   bgcolor="white" nowrap="true">
		<!-- Form fields start -->
		<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
		<tr>
			<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
			<td background="images/heading_bg.gif" class="pageName" nowrap style="background-repeat: repeat-x" valign="top" >&nbsp;Packing Details</td>
			<td background="images/heading_bg.gif" class="pageName" align="right" nowrap valign="top" style="background-repeat: repeat-x">
			<table cellpadding="0" cellspacing="0">
                      			<tr>
					  	<td class="listing-item"> From:</td>
                                    		<td nowrap="nowrap"> 
                            		<?php 
					if ($dateFrom=="") $dateFrom=date("d/m/Y");
					?>
                            			<input type="text" name="selectFrom" id="selectFrom" size="8" value="<?=$dateFrom?>" autocomplete="off" />
					</td>
					    <td class="listing-item">&nbsp;</td>
				            <td class="listing-item"> Till:</td>
                                    <td> 
                                      <? 
					   if($dateTill=="") $dateTill=date("d/m/Y");
				      ?>
                                      	<input type="text" name="selectTill" id="selectTill" size="8"  value="<?=$dateTill?>" autocomplete="off" />
					</td>
					   <td class="listing-item">&nbsp;</td>
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
                            <td class="listing-item" nowrap >&nbsp;</td>
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
	<td>
	<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$packingInstructionRecordSize;?>);" ><? }?>&nbsp;
	<? if($add==true){?>
		<!--<input type="submit" value=" Add New " name="cmdAddNew" class="button">-->
	<? }?>&nbsp;
	<? if($print==true){?>
		<input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPackingInstruction.php?selDistributorFilter=<?=$selDistributorFilter?>',700,600);">
		<!--&nbsp;&nbsp;
		<input type="button" name="btnPrintPackingAdvice" class="button" value=" Print Packing Advice " onClick="return printPkngAdvice('delId_', <?=$packingInstructionRecordSize;?>);" style="width:140px" >-->
		&nbsp;&nbsp;
		<input type="button" value=" Print Label " name="btnPrint" class="button" onClick="return cfmPrintProforma('delId_', <?=$packingInstructionRecordSize;?>);" style="width:90px">
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
		<td width="1" ></td>
		<td colspan="2" style="padding-left:10px;padding-right:10px;">
<table cellpadding="2"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?php
		if ($packingInstructionRecordSize) {
			$i	=	0;
		?>
<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="9" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"PackingInstruction.php?pageNo=$page&selDistributorFilter=$selDistributorFilter&selectFrom=$dateFrom&selectTill=$dateTill\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PackingInstruction.php?pageNo=$page&selDistributorFilter=$selDistributorFilter&selectFrom=$dateFrom&selectTill=$dateTill\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PackingInstruction.php?pageNo=$page&selDistributorFilter=$selDistributorFilter&selectFrom=$dateFrom&selectTill=$dateTill\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
											<tr  bgcolor="#f2f2f2" align="center">
												<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
	<td class="listing-head" style="padding-left:10px; padding-right:10px;">Distributor</td>
	<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Invoice No</td>
	<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Invoice<br/> Type</td>
	<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Despatch<br/>Date</td>
	<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Total <br>Gross Wt <br>(Kg)</td>
	<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>	
	<td class="listing-head" style="padding-left:10px; padding-right:10px;">Packing Details</td>
<? if($edit==true){?>
	<td class="listing-head"></td>
<? }?>
			</tr>
			<?php
			foreach ($packingInstructionRecords as $pir) {
				$i++;
				$pkngInstructionId = $pir[0];
				$sDistributorName    = $pir[6];
				$invType = $pir[2];
				$soNo 	= $pir[3];
				$pfNo 	= $pir[4];
				$saNo	= $pir[5];				
				$invoiceNo = "";
				if ($soNo!=0) $invoiceNo=$soNo;
				else if ($invType=='T') $invoiceNo = "P$pfNo";
				else if ($invType=='S') $invoiceNo = "S$saNo";	

				$selInvoiceType = "";
				if ($invType=='T' && $soNo!=0) $selInvoiceType = $invoiceTypeArr['TI'];
				else if ($invType=='T' && $pfNo!=0) $selInvoiceType = $invoiceTypeArr['PI'];
				else if ($invType=='S') $selInvoiceType = $invoiceTypeArr['SI'];	
			
				$tGrossWt	= $pir[7];
				$pkngInstStatus  = $pir[8];
				
				# --------------- Edit Section ---------------
				list($modifiedBy, $timeDiff) = $packingInstructionObj->getMainRec($pkngInstructionId);	
				$editMints	= substr($timeDiff,-5,2);
				$timeLimit 	= number_format(($refreshTimeLimit/60),2,'.',''); // C to Mints					
				if ( number_format($editMints,2,'.','')>=$timeLimit) { 					
					# Update Rec
					$updateModifiedRec = $packingInstructionObj->updatePkgInstEditingRec($pkngInstructionId, '', 'U');
				}				
				$displayEditStatus = "";
				if ($modifiedBy!=0 && $modifiedBy!="") {
					$lockedUser = $manageusersObj->getUsername($modifiedBy);
					$displayEditStatus = "Locked by $lockedUser";
				}
				# ------------------------------

				$displayPkngInstStatus = ($pkngInstStatus=='C')?"COMPLETE":"PENDING";

				$displayColor = "";
				if ($pkngInstStatus=='C') $displayColor = "#90EE90"; // LightGreen		
				else $displayColor = "white";
				
				$soId	= $pir[1];
				$despatchDate = dateFormat($pir[9]);
				$invConfirmStatus = $pir[10];

				$disableRow = "";
				if ($pkngInstStatus=='C' && $invConfirmStatus=='C') {
					$disableRow = "disabled";
				} else if (($pkngInstStatus=='C' && !$isAdmin && !$reEdit) || $modifiedBy!=0)  $disableRow = "disabled";

				$displayLink = "";
				if ($pkngInstStatus!='C' && $print) {
					$displayLink = "<a href=\"javascript:printWindow('ViewPackingInstruction.php?selSOId=$soId&PkgInsMainId=$pkngInstructionId',700,600)\" class=\"link1\" title=\"Click here to Print Packing Details.\">PRINT</a>";
				} else {
					$displayLink = "<a href=\"javascript:printWindow('ViewPackingInstruction.php?selSOId=$soId&PkgInsMainId=$pkngInstructionId',700,600)\" class=\"link1\" title=\"Click here to View Packing Details.\">VIEW</a>";
				}
			
			?>
 <tr  bgcolor="WHITE">
	<td width="20">
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$pkngInstructionId;?>" class="chkBox">
		<input type="hidden" name="hidPknginstStatus_<?=$i?>" id="hidPknginstStatus_<?=$i?>" value="<?=$pkngInstStatus?>">
		<input type="hidden" name="hidSOId_<?=$i;?>" id="hidSOId_<?=$i;?>" value="<?=$soId?>">
	</td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sDistributorName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$invoiceNo;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$selInvoiceType;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$despatchDate;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=$tGrossWt;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" bgcolor="<?=$displayColor?>"><?=$displayPkngInstStatus;?></td>	
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$displayLink?>
			<!--<a href="javascript:printWindow('ViewPackingInstruction.php?selSOId=<?=$soId?>&PkgInsMainId=<?=$pkngInstructionId?>',700,600)" class="link1" title="Click here to View Packing Details">
				VIEW DETAILS
			</a>-->
			<? if($print==true){?>
			<!--/-->
			<!--<a href="javascript:printWindow('PrintPackingLabel.php?selSOId=<?=$soId?>',700,600)" class="link1" title="Click here to Print Packing Label">
				PRINT LABEL
			</a>-->
			<?php }?>
	</td>
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$pkngInstructionId;?>,'editId');this.form.action='PackingInstruction.php';" <?=$disableRow?>>
			<?php
				if ($displayEditStatus!="") {
			?>
			<br/>
			<span class="err1" style="line-height:normal;font-size:8px;"><?=$displayEditStatus?></span>
			<? }?>
		</td>
<? }?>
		</tr>
		<?php
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="9" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"PackingInstruction.php?pageNo=$page&selDistributorFilter=$selDistributorFilter&selectFrom=$dateFrom&selectTill=$dateTill\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PackingInstruction.php?pageNo=$page&selDistributorFilter=$selDistributorFilter&selectFrom=$dateFrom&selectTill=$dateTill\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PackingInstruction.php?pageNo=$page&selDistributorFilter=$selDistributorFilter&selectFrom=$dateFrom&selectTill=$dateTill\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
											<?php
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
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$packingInstructionRecordSize;?>);" ><? }?>&nbsp;
<? if($add==true){?>
	<!--<input type="submit" value=" Add New " name="cmdAddNew" class="button">-->
<? }?>&nbsp;
<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPackingInstruction.php?selDistributorFilter=<?=$selDistributorFilter?>',700,600);">
	<!--&nbsp;&nbsp;
		<input type="button" name="btnPrintPackingAdvice" class="button" value=" Print Packing Advice " onClick="return printPkngAdvice('delId_', <?=$packingInstructionRecordSize;?>);" style="width:140px" >-->
	&nbsp;&nbsp;
	<input type="button" value=" Print Label " name="btnPrint" class="button" onClick="return cfmPrintProforma('delId_', <?=$packingInstructionRecordSize;?>);" style="width:90px">
<? }?></td>
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
<input type="hidden" name="hidSelDistributorFilter" value="<?=$selDistributorFilter?>">
		<tr>
			<td height="10"></td>
		</tr>
	</table>
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
		// Add New Aditional Item
		function addNewAdditionalItem(recSize)
		{
			addNewSOAdditionalItemRow('tblSOAdditionalItem','','');
			/*
			for (var i=0; i<recSize; i++) {
				//addNewPrdBtchNo('tblPrdBtchNo_', i, 0);
				//addNewPkngDtls('tblPkngDtls_', i, 0);
			}
			*/
		}	
		//window.load = addNewAdditionalItem('<?=sizeof($salesOrderEntryRecs)?>');
	</SCRIPT>
	<? if (sizeof($adnlItemRecs)>0) {?>
		<script language="JavaScript">
		<?php 
		//foreach ($adnlItemRecs as $air) {
		foreach ($adnlItemRecs as $itemName=>$itemWt) {
			//$itemName	= $air[1];
			//$itemWt		= $air[2];
		?>			
			addNewSOAdditionalItemRow('tblSOAdditionalItem', '<?=$itemName?>', '<?=$itemWt?>');
		<?php 
			}
		?>
		cAdditionalItem();	
		</script>
	<? } else if ($addMode || $editMode) {?>
		<script language="JavaScript">
			addNewSOAdditionalItemRow('tblSOAdditionalItem','','');
		</script>
	<? }?>
	<? if ($addMode || $editMode) {?>
	<script language="JavaScript">
		enbleCfmBtn();
	</script>
	<? }?>
	<?php
		if ($editMode) {
	?>
	<script>
		// Set time D=300
		tickTimer(<?=$refreshTimeLimit?>, '<?=$editPkngInstId?>', 'frmPackingInstruction');
	</script>
	<? }?>
	<?php
		if (!$addMode && !$editMode && sizeof($packingInstructionRecords)>0) {
	?>
	<script>
		window.load = beginrefresh('frmPackingInstruction');
	</script>
	<? }?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>
