<?php
	require("include/include.php");
	require_once("lib/DailySalesEntry_ajax.php");
	ob_start();
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$userId		= $sessObj->getValue("userId");

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

	/*
		$selectTimeHour		=	$p["selectTimeHour"];
		$selectTimeMints	=	$p["selectTimeMints"];
		$timeOption 		= 	$p["timeOption"];
		$selectTime	= $p["selectTimeHour"]."-".$p["selectTimeMints"]."-".$p["timeOption"];
	*/

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") $addMode = false;

	#Add a Daily Sales Entry
	if ($p["cmdAdd"]!="") {		
		$rtCtTableRowCount	= $p["hidMainTableRowCount"];

		$entryDate		= mysqlDateFormat($p["entryDate"]);
		$selSalesStaffId 	= $p["selSalesStaff"];	
		# Checking Entry Exist for the same date $cEntryId-Blank
		$chkEntryExist 		= $dailySalesEntryObj->chkEntryExist($entryDate, $selSalesStaffId, $cEntryId);

		if ($selSalesStaffId!="" && $rtCtTableRowCount!="" && !$chkEntryExist) {
			if ($selSalesStaffId!="") {
				$dailySalesEntryRecIns = $dailySalesEntryObj->addDailySalesMainEntry($entryDate, $selSalesStaffId, $userId);
				#Find the Last inserted Id From  t_dailysales_main Table
				$salesEntryMainId = $databaseConnect->getLastInsertedId();
			}
			# Retail Counter Entry
			for ($i=0; $i<$rtCtTableRowCount; $i++) {
				$status = $p["status_".$i];
			   	if ($status!='N') {
					$selRtCounter	=	$p["selRtCounter_".$i];
					$visitDate	=	mysqlDateFormat($p["visitDate_".$i]);
					$visitTime = $p["selectTimeHour_".$i]."-".$p["selectTimeMints_".$i]."-".$p["timeOption_".$i];
					$selSchemeId	=	$p["selScheme_".$i];
					$poNum		=	$p["poNum_".$i];
					$orderValue	=	$p["orderValue_".$i];
					$productTbleRowCount = $p["hidStkTbleRowCount_".$i];

					if ($salesEntryMainId!="" && $selRtCounter!="" && $visitDate!="" && $visitTime!="") {
						$dailySalesRtCounterRecIns = $dailySalesEntryObj->addDailySalesRtCounterEntry($salesEntryMainId, $selRtCounter, $visitDate, $visitTime, $selSchemeId, $poNum, $orderValue);
						#Find the Last inserted Id From  t_dailysales_rtcounter Table
						if ($dailySalesRtCounterRecIns)
						$dailySalesRtctEntryId = $databaseConnect->getLastInsertedId();
					}

					# Product Entry
					for ($j=1;$j<=$productTbleRowCount;$j++) {
						//$stkStatus = $p["status_".$j."_".$i]; 
       	   					//if ($stkStatus!='N') {
							$selProduct	= $p["selProduct_".$j."_".$i]; 
							$numStock	= $p["numStock_".$j."_".$i]; 
							$numOrder	= $p["numOrder_".$j."_".$i];
							$balStk		= $p["balStk_".$j."_".$i];
							if ($dailySalesRtctEntryId!="" && $selProduct!="") {
								$dailySalesProductRecIns = $dailySalesEntryObj->addDailySalesProductEntry($dailySalesRtctEntryId, $selProduct, $numStock, $numOrder, $balStk);
							}
						//}	// Produt Status !N End
					}  // Sub Loop End
				} // Retail Ct Status !N End
			   } // Main Loop End
			}
		
		if ($dailySalesEntryRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddDailySalesEntry);
			$sessObj->createSession("nextPage",$url_afterAddDailySalesEntry.$dateSelection);
		} else {
			$addMode	=	true;
			if ($chkEntryExist) $err = " Failed to add daily sales Entry.<br> Please make sure the entry date you have selected is not duplicate";
			else $err	=	$msg_failAddDailySalesEntry;
		}
		$dailySalesEntryRecIns		=	false;
	}
	

	# Edit Sales Order
	if ($p["editId"]!="" ) {
		$editId			= $p["editId"];
		$editMode		= true;
		$dailySalesEntryRec	= $dailySalesEntryObj->find($editId);		
		$editDailySalesEntryId	= $dailySalesEntryRec[0];		
		$entryDate		= dateFormat($dailySalesEntryRec[1]);
		$selSalesStaffId 	= $dailySalesEntryRec[2];
				
		# Fetch all Daily sales Entry records
		$fetchAllSalesRtCtEntryRecs = $dailySalesEntryObj->fetchAllDailySalesRtCtEntryRecs($editDailySalesEntryId);
	}


	#Update Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$salesEntryMainId = $p["hidDailySalesEntryId"];
		
		$rtCtTableRowCount	= $p["hidMainTableRowCount"];
		$entryDate		= mysqlDateFormat($p["entryDate"]);
		$selSalesStaffId 	= $p["selSalesStaff"];
		
		# Checking Entry Exist for the same date $cEntryId-Blank
		$chkEntryExist	= $dailySalesEntryObj->chkEntryExist($entryDate, $selSalesStaffId, $salesEntryMainId);		
		if ($selSalesStaffId!="" && $rtCtTableRowCount!="" && $salesEntryMainId!="" && !$chkEntryExist) {
			if ($selSalesStaffId!="") {
				$dailySalesEntryRecUptd = $dailySalesEntryObj->updateDailySalesMainEntrRec($salesEntryMainId, $entryDate, $selSalesStaffId);	
			}
			# Delete all Entries of the corresponding Sales Staff
			$deletDailySalesEntries = $dailySalesEntryObj->deleteDailySalesEntries($salesEntryMainId);
			# Retail Counter Entry
			for ($i=0; $i<$rtCtTableRowCount; $i++) {
				$status = $p["status_".$i];
			   	if ($status!='N') {
					$selRtCounter	=	$p["selRtCounter_".$i];
					$visitDate	=	mysqlDateFormat($p["visitDate_".$i]);
					$visitTime = $p["selectTimeHour_".$i]."-".$p["selectTimeMints_".$i]."-".$p["timeOption_".$i];
					$selSchemeId	=	$p["selScheme_".$i];
					$poNum		=	$p["poNum_".$i];
					$orderValue	=	$p["orderValue_".$i];
					$productTbleRowCount = $p["hidStkTbleRowCount_".$i];

					if ($salesEntryMainId!="" && $selRtCounter!="" && $visitDate!="" && $visitTime!="") {
						$dailySalesRtCounterRecIns = $dailySalesEntryObj->addDailySalesRtCounterEntry($salesEntryMainId, $selRtCounter, $visitDate, $visitTime, $selSchemeId, $poNum, $orderValue);
						#Find the Last inserted Id From  t_dailysales_rtcounter Table
						if ($dailySalesRtCounterRecIns)
						$dailySalesRtctEntryId = $databaseConnect->getLastInsertedId();
					}

					# Product Entry
					for ($j=1;$j<=$productTbleRowCount;$j++) {
						//$stkStatus = $p["status_".$j."_".$i]; 
       	   					//if ($stkStatus!='N') {
							$selProduct	= $p["selProduct_".$j."_".$i]; 
							$numStock	= $p["numStock_".$j."_".$i]; 
							$numOrder	= $p["numOrder_".$j."_".$i];
							$balStk		= $p["balStk_".$j."_".$i];
							if ($dailySalesRtctEntryId!="" && $selProduct!="") {
								$dailySalesProductRecIns = $dailySalesEntryObj->addDailySalesProductEntry($dailySalesRtctEntryId, $selProduct, $numStock, $numOrder, $balStk);
							}
						//}	// Produt Status !N End
					}  // Sub Loop End
				} // Retail Ct Status !N End
			   } // Main Loop End
			}
				
		if ($dailySalesEntryRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succDailySalesEntryUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateDailySalesEntry.$dateSelection);
		} else {
			$editMode	=	true;
			if ($chkEntryExist) $err = " Failed to update daily sales Entry.<br> Please make sure the entry date you have selected is not duplicate";
			else $err	=	$msg_failDailySalesEntryUpdate;
		}
		$dailySalesEntryRecUptd	=	false;
	}
	

	# Delete Sales Order
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$dailySalesEntryId	=	$p["delId_".$i];
			
			if ($dailySalesEntryId!="") {
				# Delete all Entries of the corresponding Sales Staff
				$deletDailySalesEntries = $dailySalesEntryObj->deleteDailySalesEntries($dailySalesEntryId);
				// Delete Daily sales  main
				$dailySalesEntryRecDel = $dailySalesEntryObj->deleteDailySalesEntryMainRec($dailySalesEntryId);
			}
		}
		if ($dailySalesEntryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDailySalesEntry);
			$sessObj->createSession("nextPage",$url_afterDelDailySalesEntry.$dateSelection);
		} else {
			$errDel	=	$msg_failDelDailySalesEntry;
		}
		$dailySalesEntryRecDel	=	false;
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

	#List all Daily Sales Entry
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$dailySalesEntryRecords = $dailySalesEntryObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);		
		$dailySalesEntryRecSize = sizeof($dailySalesEntryRecords);
		// For Pagination
		$fetchAllDailySalesEntryRecs = $dailySalesEntryObj->fetchAllDateRangeRecords($fromDate, $tillDate);
		
	}

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllDailySalesEntryRecs);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# List all Sales Staff
	//$salesStaffResultSetObj = $salesStaffMasterObj->fetchAllRecords();
	$salesStaffResultSetObj = $salesStaffMasterObj->fetchAllRecordsActiveStaff();

	# List all RetailCounter
	//$retailCounterResultSetObj = $retailCounterMasterObj->fetchAllRecords('');
	$retailCounterResultSetObj = $retailCounterMasterObj->fetchAllRecordsActiveRetailCounter('');
	# List all Combo Matrix Product
	//$comboMatrixResultSetObj = $comboMatrixObj->fetchAllRecords();
	$productPriceRateListId = $productPriceRateListObj->latestRateList();
	if ($addMode) $getMrpProductRecs = $dailySalesEntryObj->fetchMrpProductRecs($productPriceRateListId);

	# List all Scheme Master
	//$schemeMasterResultSetObj = $schemeMasterObj->fetchAllRecords();
	$schemeMasterResultSetObj = $schemeMasterObj->fetchAllRecordsActiveScheme();

	# mode Setting
	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/DailySalesEntry.js"; // For Printing JS in Head SCRIPT section

	if ($editMode) $heading	= $label_editDailySalesEntry;
	else	       $heading	= $label_addDailySalesEntry;
	
	# Include Template [topLeftNav.php]
	//require("template/topLeftNav.php");
?>
	<form name="frmDailySalesEntry" action="DailySalesEntry.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailySalesEntry.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDailySalesEntry(document.frmDailySalesEntry);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailySalesEntry.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDailySalesEntry(document.frmDailySalesEntry);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
			<input type="hidden" name="hidDailySalesEntryId" value="<?=$editDailySalesEntryId;?>">
											
											<tr>
											  <td class="fieldName" nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>
						<tr>
						  <td colspan="2" nowrap style="padding-left:5px;padding-right:5px;">
					<table cellpadding="0" cellspacing="0">
					<tr>
                                               <td class="fieldName" nowrap="nowrap">*Entry Date&nbsp;</td>
                                                      <td nowrap>
								<? if($p["entryDate"]!="") $entryDate=$p["entryDate"];?>
								<input type="text" name="entryDate" id="entryDate" size="8" value="<?=$entryDate?>" autocomplete="off">
							</td>
                                                    </tr>	
                                                <tr>
                                                  <td class="fieldName" nowrap>*Sales Staff&nbsp;</td>
                                                  <td class="listing-item">
					<select name="selSalesStaff" id="selSalesStaff">			
                                        <option value="">-- Select --</option>
					<?	
					while ($ssr=$salesStaffResultSetObj->getRow()) {
						$salesStaffId	 = $ssr[0];
						$salesStaffCode = stripSlash($ssr[1]);
						$salesStaffName = stripSlash($ssr[2]);
						$selected = "";
						if ($selSalesStaffId==$salesStaffId) $selected = "selected";	
					?>
                            		<option value="<?=$salesStaffId?>" <?=$selected?>><?=$salesStaffName?></option>
					<? }?>
					</select>
					</td>
                        </tr>						
                                              </table>
						</td>
					</tr>
					<tr>
					  <td colspan="2" nowrap height="5">&nbsp;</td>
					</tr>	
<tr>
	<TD style="padding-left:5px;padding-right:5px;">
		<a href="###" id='addRow' onclick="javascript:addNewSOItem(0);"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;'>Add New Retailer</a>
	</TD>
</tr>				
<tr><TD height="5"></TD></tr>
<!--  Dynamic Row Starting Here-->
<tr>
	<TD style="padding-left:5px;padding-right:5px;" align="center" colspan="2">
		<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddRtCounter" >
	                <tr bgcolor="#f2f2f2" align="center">
				<td class="listing-head" style="padding-left:2px;padding-right:2px;">Retail Counter</td>
			  	<td class="listing-head" style="padding-left:2px;padding-right:2px;">Date <br>of Visit</td>
                                <td class="listing-head" style="padding-left:2px;padding-right:2px;">Time of Visit</td>
				<td class="listing-head" style="padding-left:2px;padding-right:2px;">Display<br>Charge</td>
                                <td class="listing-head" style="padding-left:2px;padding-right:2px;">Stock</td>
				<td class="listing-head" style="padding-left:2px;padding-right:2px;">Schemes</td>
				<td class="listing-head" style="padding-left:2px;padding-right:2px;">PO No.</td>
				<td class="listing-head" style="padding-left:2px;padding-right:2px;">Order Value</td>
				<td></td>
                        </tr>
			<tr bgcolor="#FFFFFF" align="center">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>							
                                <td class="listing-head" align="right" colspan="3" nowrap="true">TOTAL PACKS SOLD:</td>
                                <td>
					<input name="totalPacksSold" type="text" id="totalPacksSold" size="8" style="text-align:right" readonly value="<?=$totalPacksSold;?>">
				</td>
				<td></td>
		        </tr>
			<tr bgcolor="#FFFFFF" align="center">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>								
                                <td class="listing-head" align="right" colspan="3">TOTAL VALUE OF ORDER COLLECTED:</td>
                                <td>
					<input name="totalValueOrder" type="text" id="totalValueOrder" size="8" style="text-align:right" readonly value="<?=$totalAmount;?>">
				</td>
				<td></td>
		        </tr>
		</table>	
	</TD>
</tr>
<input type='hidden' name="hidMainTableRowCount" id="hidMainTableRowCount">
<!--  Dynamic Row Ends Here-->
<tr><TD height="5"></TD></tr>
<tr>
	<TD style="padding-left:5px;padding-right:5px;">
		<a href="###" id='addRow' onclick="javascript:addNewSOItem(0);"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;'>Add New Retailer</a>
	</TD>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailySalesEntry.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDailySalesEntry(document.frmDailySalesEntry);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailySalesEntry.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDailySalesEntry(document.frmDailySalesEntry);">&nbsp;&nbsp;												</td>
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Sales Entry </td>
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
			<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailySalesEntryRecSize;?>);"><?}?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDailySalesEntry.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?></td>
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
	if (sizeof($dailySalesEntryRecords)>0) {
		$i = 0;
	?>
	<? if($maxpage>1){?>
                <tr  bgcolor="#f2f2f2" align="center">
                <td colspan="5" bgcolor="#FFFFFF" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		$nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DailySalesEntry.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DailySalesEntry.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DailySalesEntry.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;" nowrap>Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Sales Staff</td>	
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Visited Retail<br> Counters</td>	
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($dailySalesEntryRecords as $dse) {
		$i++;
		$dailySalesEntryId	= $dse[0];
		$entryDate	= dateFormat($dse[1]);
		$salesStaffName = $dse[3];	
		# get visisted Rt Counter
		$getVisitedRtCounter = $dailySalesEntryObj->getVisitedRtCounter($dailySalesEntryId);
	?>
	<tr  bgcolor="WHITE">
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailySalesEntryId;?>" class="chkBox">			
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$entryDate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$salesStaffName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<table>
				<tr>
				<?
					$numLine = 3;
					if (sizeof($getVisitedRtCounter)>0) {
						$nextRec	=	0;
						$k=0;
						foreach ($getVisitedRtCounter as $rtCt) {
							$j++;
							$rtCtName = $rtCt[1];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$rtCtName?></td>
					<? if($nextRec%$numLine == 0) { ?>
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
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$dailySalesEntryId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='DailySalesEntry.php';">
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
         	<td colspan="5" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DailySalesEntry.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DailySalesEntry.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DailySalesEntry.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</table></td>
	</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailySalesEntryRecSize;?>);"><?}?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDailySalesEntry.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
		</td>
		</tr>
		</table></td>
								</tr>
								<tr>
		<td colspan="3" height="5" >
		<input type="hidden" name="editMode" value="<?=$editMode?>">		
		</td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>
		
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	<SCRIPT LANGUAGE="JavaScript">
		function addNewSOItem(val)
		{
			//if (val=="") val=0;
			addNewRtCounterRow('tblAddRtCounter','','','', '', '','', 1,val);		
		}
	</SCRIPT>

<? if ($addMode) {?>
	<SCRIPT LANGUAGE="JavaScript">
		window.load = addNewSOItem(1);
	</SCRIPT>
<? }?>

<?
	if (sizeof($fetchAllSalesRtCtEntryRecs)>0) {	
		$i = 0;	
		foreach ($fetchAllSalesRtCtEntryRecs as $rec) {
			$retailCtEntryId = $rec[0];
			$selRtCounterId  = $rec[1];
			$visitDate	= dateFormat($rec[2]);
			$visitTime	= $rec[3];
			$schemeId	= $rec[4];
			$poNum		= $rec[5];
			$orderValue	= $rec[6];
			# Get Selected product
			$getMrpProductRecs = $dailySalesEntryObj->fetchMRPSelectedProductRecs($retailCtEntryId, $productPriceRateListId);
			# Get Product Entry Recrds
			//$getProductSalesRecs = $dailySalesEntryObj->fetchAllDailySalesProductEntryRecs($retailCtEntryId);
?>
	<SCRIPT LANGUAGE="JavaScript">
		addNewRtCounterRow('tblAddRtCounter','<?=$selRtCounterId?>','<?=$visitDate?>','<?=$visitTime?>', '<?=$schemeId?>', '<?=$poNum?>', '<?=$orderValue?>','<?=$mode?>','1');
		// Get Schemes
		xajax_chkRtSchemeEligible('<?=$selRtCounterId?>','<?=$i?>');
		// Get Dis Charge
		xajax_disChargeEligible('<?=$selRtCounterId?>','<?=$i?>');
		// Calc order Value
		calcProductOrderedValue();
	</SCRIPT>
<?
	$i++;
	}
   }
?>
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
			inputField  : "entryDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "entryDate", 
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
			inputField  : "visitDate_",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "visitDate_", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	</form>
<?
$out1 = ob_get_contents(); 
ob_end_clean();
?>
<?
# Include Template [topLeftNav.php]
require("template/topLeftNav.php");
	echo $out1;
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
?>