<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	
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
	
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}

	#Resetting values
	if ($p["selDistributor"]!="") $selDistributorId = $p["selDistributor"];
	$retailCounterStkRecSize = "";

	#Add a rec
	if ($p["cmdAdd"]!="") {
		$selDate	= mysqlDateFormat($p["selDate"]);
		$selDistributorId = $p["selDistributor"];
		$selRetailCounter = $p["selRetailCounter"];
		
		$rowCount	 = $p["hidTableRowCount"]; // Row Count	
		
		if ($selDistributorId!="") {
			$retailCounterStockRecIns = $retailCounterStockObj->addRetailCounterStock($selDate, $selDistributorId, $selRetailCounter, $userId);
			#Find the Last inserted Id From ing_purchaseorder Table
			$lastId = $databaseConnect->getLastInsertedId();
		}
			
		for ($i=0; $i<$rowCount; $i++) {
			$status = $p["status_".$i];
			if ($status!='N') {
				$selProductId	= $p["selProduct_".$i];
				$availableQty	= trim($p["availableQty_".$i]);
				$usedQty	= trim($p["usedQty_".$i]);
				$balanceQty	= $p["balanceQty_".$i];
			
				if ($lastId!="" && $selProductId!="" && $availableQty!="" && $usedQty!="") {
					$retailCounterStkProductIns = $retailCounterStockObj->addRetailCounterStockEntries($lastId, $selProductId, $availableQty, $usedQty, $balanceQty);
				}
			}
		}
	
		if ($retailCounterStockRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddRetailCounterStock);
			$sessObj->createSession("nextPage",$url_afterAddRetailCounterStock.$dateSelection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddRetailCounterStock;
		}
		$retailCounterStockRecIns		=	false;
	}
	

	# Edit a rec
	if ($p["editId"]!="" ) {
		$editId			= $p["editId"];
		$editMode		= true;
		$retailCounterStockRec	= $retailCounterStockObj->find($editId);		
		$editRetailCounterStockId	= $retailCounterStockRec[0];	
		$selDate		= dateFormat($retailCounterStockRec[1]);
		if ($p["editSelectionChange"]=='1' || $p["selDistributor"]=="") {
			$selDistributorId = $retailCounterStockRec[2];
		} else {
			$selDistributorId = $p["selDistributor"];
		}	

		$selRetailCounter	= $retailCounterStockRec[3];
		$retailCounterStockRecs = $retailCounterStockObj->fetchAllRCStockItem($editRetailCounterStockId);
		$retailCounterStkRecSize = sizeof($retailCounterStockRecs);
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$retailCounterStockId = $p["hidRetailCounterStkId"];

		$selDate	  = mysqlDateFormat($p["selDate"]);
		$selDistributorId = $p["selDistributor"];
		$selRetailCounter = $p["selRetailCounter"];

		$rowCount	 = $p["hidTableRowCount"]; // Row Count	
				
		if ($retailCounterStockId!="" && $selDistributorId!="") {
			$retailCounterStockRecUptd = $retailCounterStockObj->updateRetailCounterStockRec($retailCounterStockId,$selDate, $selDistributorId, $selRetailCounter);
					
			$retailCounterStkEntryId = "";
			for ($i=0; $i<$rowCount; $i++) {
				$retailCounterStkEntryId = $p["retailCounterStkEntryId_".$i];
				$status = $p["status_".$i];
				if ($status!='N') {
					$selProductId	= $p["selProduct_".$i];
					$availableQty	= trim($p["availableQty_".$i]);
					$usedQty	= trim($p["usedQty_".$i]);
					$balanceQty	= $p["balanceQty_".$i];
						
					if ($retailCounterStockId!="" && $selProductId!="" && $availableQty!="" && $usedQty!="" && $retailCounterStkEntryId=="") {
						// If New product Added then insert Record
						$retailCounterStkProductIns = $retailCounterStockObj->addRetailCounterStockEntries($retailCounterStockId, $selProductId, $availableQty, $usedQty, $balanceQty);
					} else if ($retailCounterStkEntryId!="" && $selProductId!="" && $availableQty!="" && $usedQty!="") {
						// If existing Update
						$updateRetailCounterStockEntries = $retailCounterStockObj->updateRetailCounterStockEntries($retailCounterStkEntryId, $selProductId, $availableQty, $usedQty, $balanceQty);
					}
				}

				# Delete RC Stk
				if ($status=='N' && $retailCounterStkEntryId!="") {
					$deleteRCStkItem = $retailCounterStockObj->deleteRCStkItem($retailCounterStkEntryId);
				}
			}
		}	
		if ($retailCounterStockRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succRetailCounterStockUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateRetailCounterStock.$dateSelection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failRetailCounterStockUpdate;
		}
		$retailCounterStockRecUptd	=	false;
	}
	

	# Delete a rec
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$retailCounterStockId	=	$p["delId_".$i];

			if ($retailCounterStockId!="" ) {
				// Delete from Entry Rec
				$deleteRetailCounterStockEntryRecs =	$retailCounterStockObj->deleteRetailCounterStockEntryRecs($retailCounterStockId);
				// Delete main
				$retailCounterStockRecDel = $retailCounterStockObj->deleteRetailCounterStockRec($retailCounterStockId);
			}
		}
		if ($retailCounterStockRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelRetailCounterStock);
			$sessObj->createSession("nextPage",$url_afterDelRetailCounterStock.$dateSelection);
		} else {
			$errDel	=	$msg_failDelRetailCounterStock;
		}
		$retailCounterStockRecDel	=	false;
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

		$retailCounterStockRecords = $retailCounterStockObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);		
		$retailCounterStockRecSize = sizeof($retailCounterStockRecords);

		// For Pagination
		$fetchAllRetailCounterStock = $retailCounterStockObj->fetchAllDateRangeRecords($fromDate, $tillDate);
		
	}

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllRetailCounterStock);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# List all Distributor
	$distributorResultSetObj = $distributorMasterObj->fetchAllRecords();

	# List all Retail Counter
	if ($selDistributorId!="") {
		$retailCounterRecs = $retailCounterStockObj->filterRetailCounterRecs($selDistributorId);

		#Product Price Rate List
		//$productPriceRateListId = $productPriceRateListObj->latestRateList();
		# Filter Product
		$productMasterRecords = $retailCounterStockObj->filterProductRecs($selDistributorId);
	}

	if ($editMode) $heading	= $label_editRetailCounterStock;
	else	       $heading	= $label_addRetailCounterStock;
	
	$ON_LOAD_PRINT_JS	= "libjs/RetailCounterStock.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmRetailCounterStock" action="RetailCounterStock.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr><TD height="10"></TD></tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Retail counter stock";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="50%">
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="96%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php							
								$entryHead = $heading;
								require("template/rbTop.php");
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
								</tr>-->
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RetailCounterStock.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRetailCounterStock(document.frmRetailCounterStock);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RetailCounterStock.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateRetailCounterStock(document.frmRetailCounterStock);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
		<input type="hidden" name="hidRetailCounterStkId" value="<?=$editRetailCounterStockId;?>">
		<tr><TD height="10"></TD></tr>
						<tr>
						  <td colspan="2" nowrap>
					<table width="200">
                                                <tr>
                                                  <td class="fieldName">Date</td>
                                                  <td class="listing-item">
							<? if($p["selDate"]!="") $selDate=$p["selDate"];?>
							<input type="text" name="selDate" id="selDate" size="8" value="<?=$selDate?>" autocomplete="off">
						</td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName">*Distributor</td>
                                                  <td class="listing-item">
					<select name="selDistributor" id="selDistributor" onchange="<? if ($addMode) {?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<? }?>">
                                        <option value="">-- Select --</option>
					<?php	
					while ($dr=$distributorResultSetObj->getRow()) {
						$distributorId	 = $dr[0];
						$distributorName = stripSlash($dr[2]);	
						$selected = ($selDistributorId==$distributorId)?"selected":"";	
					?>
                            		<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
					<? }?>
					</select>
					</td>
                                                </tr>
					<tr>
	                                        <td class="fieldName" nowrap="nowrap">*Retail Counter</td>
                                                <td nowrap="true">
						  <? if($p["selRetailCounter"]!="") $selRetailCounter=$p["selRetailCounter"]; ?>
						  <select name="selRetailCounter" id="selRetailCounter">
                                                 <option value="">-- select --</option>
                                                 <?
						foreach ($retailCounterRecs as $rcr) {
							$retailCounterId = $rcr[0];
							$retailCounterName = $rcr[2];
							$selected = ($selRetailCounter==$retailCounterId)?"Selected":""; 
						?>
                                                <option value="<?=$retailCounterId?>" <?=$selected?>><?=$retailCounterName?></option>
                                                  <? }?>
                                                      </select></td>
                                                    </tr>					
                                              </table>
						</td>
					</tr>
		<tr><TD height="10"></TD></tr>					
		<tr>
			<td colspan="2" nowrap>
				<table >
				<TR>
				<TD style="padding-left:5px; padding-right:5px;">
					  <table  cellspacing="1" cellpadding="3" id="tblRCSItem" class="newspaperType">
                                            <tr align="center">
                                                  <th class="listing-head" nowrap="true">Product</th>  
						  <th class="listing-head" nowrap="true">Available Qty</th>	
                                                  <th class="listing-head" nowrap="true">Used Qty</th>
						  <th class="listing-head" nowrap="true">Balance Qty</th>
						  <th class="listing-head">&nbsp;</th>
                                            </tr>
		<?php
		$m = 0;
		foreach ($retailCounterStockRecs as $rec) {
			$retailCounterStkEntryId = $rec[0];	
			$editProductId	= $rec[2];
			$availableQty	= $rec[3];
			$usedQty	= $rec[4];
			$balanceQty	= $rec[5];
		?>
                        <tr align="center" id="row_<?=$m?>" class="whiteRow">
                               <td nowrap="true">
				<select name="selProduct_<?=$m?>" id="selProduct_<?=$m?>" style='width:180px;'>
                                 <option value="">-- Select --</option>
                                 <?
				foreach ($productMasterRecords as $pmr) {
					$productId	=	$pmr[0];
					$productCode	=	$pmr[1];
					$productName	=	$pmr[2];
					$selected = ($editProductId==$productId)?"Selected":"";
				?>
				<option value="<?=$productId?>" <?=$selected?>><?=$productName?></option>
				<? }?>
                                 </select>
				</td>
                                 <td nowrap>					
					<input name="availableQty_<?=$m?>" type="text" id="availableQty_<?=$m?>" value="<?=$availableQty;?>" size="6" style="text-align:right" autoComplete="off" onKeyUp="balanceRetailCounterStock();">
				</td>			
                                 <td nowrap>
					<input name="usedQty_<?=$m?>" type="text" id="usedQty_<?=$m?>" size="6" style="text-align:right" value="<?=$usedQty?>" onKeyUp="balanceRetailCounterStock();">
				</td>
                                <td nowrap="true">
					<input name="balanceQty_<?=$m?>" type="text" id="balanceQty_<?=$m?>" size="8" readonly style="text-align:right" value="<?=$balanceQty?>">
				</td>
				<td nowrap="true">
					<a href='###' onClick="setItemStatus(<?=$m?>);" ><img title="Click here to remove this item" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>
					<input name='status_<?=$m?>' type='hidden' id='status_<?=$m?>' value="" />
					<input name='IsFromDB_<?=$m?>' type='hidden' id='IsFromDB_<?=$m?>' value='N' />
					<input type="hidden" name="retailCounterStkEntryId_<?=$m?>" value="<?=$retailCounterStkEntryId?>" />	
				</td>
		           </tr>
		<?php
			$m++;
			}
		?>   

                   </table>
					<!---  table 2 end Here-->
					<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$m?>">
						</TD>			
						</TR>
				<tr><TD height="5"></TD></tr>
				<tr>
					<TD nowrap style="padding-left:5px; padding-right:5px;">
						<a href="###" id='addRow' onclick="javascript:addNewItemRow();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New</a>
					</TD>
				</tr>
						</table>
					<!-- End Here 1	 -->
						</td>
						   </tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RetailCounterStock.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRetailCounterStock(document.frmRetailCounterStock);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RetailCounterStock.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateRetailCounterStock(document.frmRetailCounterStock);">&nbsp;&nbsp;												</td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
												<input type="hidden" name="stockType" value="<?=$stockType?>" />
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>
							<?php
								require("template/rbBottom.php");
							?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->	
			</td>
		</tr>
		<?php
			}
			# Listing Category Starts
		?>
		</table>
		</td>
	</tr>		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
<tr>
				<td colspan="3" align="center">
						<table width="35%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="4" cellspacing="4">
					  <tr>
					<td nowrap="nowrap" style="padding:5px;">
					<table cellpadding="0" cellspacing="0">
                      			<tr>
					  	<td class="listing-item"> From&nbsp;</td>
                                    		<td nowrap="nowrap"> 
                            		<? 
					if ($dateFrom=="") $dateFrom=date("d/m/Y");
					?>
                            <input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>"></td>
					    <td class="listing-item">&nbsp;</td>
				            <td class="listing-item"> Till&nbsp;</td>
                                    <td> 
                                      <? 
					   if($dateTill=="") $dateTill=date("d/m/Y");
				      ?>
                                      <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>"></td>
					   <td class="listing-item">&nbsp;</td>
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
                            <td class="listing-item" nowrap >&nbsp;</td>
                          </tr>
                    </table>
		</td></tr>
	</table>
		<?php
			require("template/rbBottom.php");
		?>
		</td>
		</tr>
		</table>
				</td>
			</tr>
		<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Retail counter stock   </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
						<tr>
								  <td colspan="3" align="right" style="padding-right:10px;">
								  </td> </tr>
			<tr>
			<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$retailCounterStockRecSize;?>);"><?}?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintRetailCounterStock.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?></td>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="2"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
	if (sizeof($retailCounterStockRecords)>0) {
		$i = 0;
	?>
	<thead>
	<? if($maxpage>1){?>
                <tr align="center">
                <td colspan="5" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		$nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RetailCounterStock.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RetailCounterStock.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RetailCounterStock.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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

	<tr align="center">
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Date</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Distributor</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Retail Counter</th>		
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?
	foreach ($retailCounterStockRecords as $rcs) {
		$i++;
		$retailCounterStockId	= $rcs[0];
		$selectedDate	= dateFormat($rcs[1]);
		$distributorName = $rcs[4];
		$retailCounterName = $rcs[5];	
	?>
	<tr>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$retailCounterStockId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selectedDate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$distributorName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$retailCounterName;?></td>			
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$retailCounterStockId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='RetailCounterStock.php';">
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
		<tr>
         	<td colspan="5" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RetailCounterStock.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RetailCounterStock.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RetailCounterStock.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
	<tr>
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</tbody>
	</table>
	</td>
	</tr>
		<tr>
			<td colspan="3" height="5" ></td>
		</tr>
								<tr >
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$retailCounterStockRecSize;?>);"><?}?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintRetailCounterStock.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
		</td>
		</tr>
		</table></td>
								</tr>
								<tr>
		<td colspan="3" height="5" >
		<input type="hidden" name="editMode" value="<?=$editMode?>">
		</td>
								</tr>
							</table>		
							<?php
								include "template/boxBR.php"
							?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>
		
		<tr>
			<td height="10"></td>
		</tr>
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
			inputField  : "selDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
<? if ($addMode || $editMode) {?>
	<script>
		//balanceRetailCounterStock();	//Multiply the row
	</script>
<? }?>

	<?php 
		if ($addMode || $editMode ) {
	?>
		<script language="JavaScript">
			function addNewItemRow()
			{
				addNewRCSRow('tblRCSItem', '', '', '');
			}		
		</script>
	<?php 
		}
	?>

	<?php
		if ($addMode) {
	?>
	<script language="JavaScript">
		window.onLoad = addNewItemRow();
	</script>
	<?php
		 }
	?>
<?php
if ($retailCounterStkRecSize>0) {
?>
<script language="JavaScript" type="text/javascript">
fieldId = '<?=$retailCounterStkRecSize?>';
</script>
<?php
}
?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>