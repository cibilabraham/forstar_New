<?php
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$userId		=	$sessObj->getValue("userId");	
	$dateSelection = "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
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

	
	#Add
	if ($p["cmdAdd"]!="" ) {	
		$selDate	= mysqlDateFormat($p["selDate"]);	
		$selDistributor	= $p["selDistributor"]; 
		$amount		= $p["amount"];
		$debit		= ($p["debit"]!="")?$p["debit"]:"C";
		$amtDescription	= $p["amtDescription"];

		
		if ($selDate!="" && $selDistributor!="" && $amount!="") {
			//(Using in Sales Order and Claim)
			$distributorAccountRecIns = $distributorAccountObj->addDistributorAccount($selDate, $selDistributor, $amount, $debit, $amtDescription, $userId, '', '');
		}

		if ($distributorAccountRecIns) {
			$addMode	= false;
			$sessObj->createSession("displayMsg",$msg_succAddDistributorAccount);
			$sessObj->createSession("nextPage",$url_afterAddDistributorAccount.$dateSelection);
		} else {
			$addMode	= true;
			$err		= $msg_failAddDistributorAccount; 
		}
		$distributorAccountRecIns		=	false;
	}
	
	
	# Edit a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId			= $p["editId"];
		$editMode		= true;
		$distributorAccountRec	= $distributorAccountObj->find($editId);
		
		$editDistributorAccountId	= $distributorAccountRec[0];
		$selDate 			= dateFormat($distributorAccountRec[1]);
		$selDistributorId	= $distributorAccountRec[2];
		$amount		= $distributorAccountRec[3];
		$selCoD		= $distributorAccountRec[4];
		$debitChk	= ($selCoD=="D")?"Checked":"";
		$amtDescription	= $distributorAccountRec[5];
	
	}


	#Update A Record
	if ($p["cmdSaveChange"]!="") {	
	
		$distributorAccountId	= $p["hidDistributorAccountId"];
		
		$selDate	= mysqlDateFormat($p["selDate"]);	
		$selDistributor	= $p["selDistributor"]; 
		$amount		= $p["amount"];
		$debit		= ($p["debit"]!="")?$p["debit"]:"C";

		$amtDescription	= $p["amtDescription"];

		$hidAmount	= $p["hidAmount"];
		$selCoD		= $p["selCoD"];	
		
		if ($distributorAccountId!="" && $selDate!="" && $selDistributor!="") {
			$distributorAccountRecUptd = $distributorAccountObj->updateDistributorAccount($distributorAccountId, $selDate, $selDistributor, $amount, $debit, $amtDescription);	

			if ($amount!=$hidAmount ||  $debit!=$selCoD ) {	
				
				# Rollback Old Rec
				$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selCoD, $hidAmount);
								
				# Update Dist Rec
				$manageDistAccount = $distributorAccountObj->manageDistributorAccount($selDistributor, $debit, $amount);		
			}
		}
	
		if ($distributorAccountRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succDistributorAccountUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateDistributorAccount.$dateSelection);
		} else {
			$editMode	=	true;
			$err	= $msg_failDistributorAccountUpdate; 
		}
		$distributorAccountRecUptd	=	false;
	}


	# Delete a Record
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$distributorAccountId = $p["delId_".$i];
			
			if ($distributorAccountId!="") {
				# Get the Deleting dist account Rec
				list($selDistributor, $billAmount, $selCoD, $salesOrderId, $claimId) = $distributorAccountObj->getDistributorAccountRec($distributorAccountId);
				# Update Distributor Account	
				if ($selDistributor!="" && $billAmount!="") {	
					# Rollback Old Rec
					$updateDistAc = $distributorAccountObj->updateDistributorAmt($selDistributor, $selCoD, $billAmount);
					# Update Sales Order Rec	
					if ($salesOrderId!=0) {
						$updateSalesOrderPaymentStatus = $distributorAccountObj->updateSOPaymentStatus($salesOrderId);
					}	
					if ($claimId!=0) {
						$updateClaimPaymentStatus = $distributorAccountObj->updateClaimPaymentStatus($claimId);
					}
				}
				/* Need t o check it is linked with anyother process */
				// Delete From main Table
				$distributorAccountRecDel   = $distributorAccountObj->deleteDistributorAccount($distributorAccountId);	
			}
		}
		if ($distributorAccountRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDistributorAccount);
			$sessObj->createSession("nextPage",$url_afterDelDistributorAccount.$dateSelection);
		} else {
			$errDel	=	$msg_failDelDistributorAccount;
		}
		$distributorAccountRecDel	=	false;
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

		#List all Records
		$distributorAccountRecords = $distributorAccountObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$productionPlanningRecSize = sizeof($distributorAccountRecords);

		# Pagination
		$fetchAllDistributorAccountRecs = $distributorAccountObj->fetchDateRangeRecords($fromDate, $tillDate);
	}

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllDistributorAccountRecs);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	

	# List all Distributor
	$distributorResultSetObj = $distributorMasterObj->fetchAllRecords();

	if ($editMode)	$heading = $label_editDistributorAccount;
	else 		$heading = $label_addDistributorAccount;

	# On Load Print JS	
	$ON_LOAD_PRINT_JS	= "libjs/SupplierLedgerAccount.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSupplierLedgerAccount" action="SupplierLedgerAccount.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="75%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%"  bgcolor="#D3D3D3">
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierLedgerAccount.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDistributorAccount(document.frmSupplierLedgerAccount);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierLedgerAccount.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDistributorAccount(document.frmSupplierLedgerAccount);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
			<input type="hidden" name="hidDistributorAccountId" value="<?=$editDistributorAccountId;?>">
											
											<tr>
											  <td class="fieldName" nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>
	<tr>
		  <td colspan="2" nowrap>
					<table width="200">
						<tr>
                                                  <td class="fieldName" nowrap>*Date: </td>
                                                  <td class="listing-item">
							<input name="selDate" type="text" id="selDate" value="<?=$selDate?>" size="9" autoComplete="off" />
						</td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName">*Distributor</td>
                                                  <td class="listing-item">
							<select name="selDistributor" id="selDistributor">	
							<option value="">-- Select --</option>
							<?	
							if ($distributorResultSetObj->getNumRows()>0) {
								while ($dr=$distributorResultSetObj->getRow()) {
									$distributorId	 = $dr[0];		
									$distributorName = stripSlash($dr[2]);	
									$selected = "";
									if ($selDistributorId==$distributorId) $selected = "selected";	
							?>
							<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
							<? 
								}
							}
							?>
							</select>
					</td>
                                                </tr>						
						<tr>
						<TD class="fieldName" nowrap="true">*Amount</TD>
						<td>
							<input type="text" name="amount" id="amount" value="<?=$amount?>" size="6" style="text-align:right;">
							<input type="hidden" name="hidAmount" id="hidAmount" value="<?=$amount?>" size="6" style="text-align:right;">	
						</td>
					</tr>
					<tr>
						<TD class="fieldName">Debit</TD>
						<td>
							<input type="checkbox" name="debit" id="debit" value="D" <?=$debitChk?> class="chkBox">
						</td>
					</tr>
					<tr>
							<TD class="fieldName">*Description</TD>
							<td>
								<textarea name="amtDescription"><?=$amtDescription?></textarea>
							</td>
						</tr>
                                       </table>
					</td>
				  </tr>
				<tr><TD height="5"></TD></tr>				
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierLedgerAccount.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDistributorAccount(document.frmSupplierLedgerAccount);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierLedgerAccount.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDistributorAccount(document.frmSupplierLedgerAccount);">&nbsp;&nbsp;												</td>
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Supplier's Account</td>
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
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productionPlanningRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDistributorAccount.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($distributorAccountRecords)>0) {
		$i	=	0;
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
      				$nav.= " <a href=\"SupplierLedgerAccount.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SupplierLedgerAccount.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SupplierLedgerAccount.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Distributor</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Particulars</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Credit/Debit</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Amount</td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
		<?
		foreach ($distributorAccountRecords as $dar) {
			$i++;
			$distributorAccountId	= $dar[0];
			$selectDate		= dateFormat($dar[1]);
			$distributorName	= $dar[6];
			$particulars		= $dar[5];
			$amount			= $dar[3];
			$cod			= $dar[4];				
			$disCod		= "";
			if ($cod=="C")  $disCod = "Credit";
			else 		$disCod = "Debit";
		?>
		<tr  bgcolor="WHITE">
			<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$distributorAccountId;?>" class="chkBox"></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selectDate;?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$distributorName;?></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="left">
				<?=$particulars?>
			</td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="left">
				<?=$disCod?>
			</td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
				<?=$amount?>
			</td>
			<? if($edit==true){?>
			<td class="listing-item" width="60" align="center">			
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$distributorAccountId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SupplierLedgerAccount.php';"></td>
			<? }?>
		</tr>
		<?
			}
		?>
			<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
			<input type="hidden" name="editId" value="<?=$editId?>">
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
      				$nav.= " <a href=\"SupplierLedgerAccount.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SupplierLedgerAccount.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SupplierLedgerAccount.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" >
	<input type="hidden" name="hidSelProduct" value="<?=$selProduct?>">	
	<input type="hidden" name="hidProductGmsPerPouch" id="hidProductGmsPerPouch" value="<?=$productGmsPerPouch?>">
	<input type="hidden" name="totalFixedFishQty" id="totalFixedFishQty" value="<?=$totalFixedFishQty?>">	
	</td>
	</tr>
	<tr>	
	<td colspan="3">
		<table cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td>
			<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productionPlanningRecSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDistributorAccount.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
		</td>
		</tr>
		</table></td></tr>
		<tr>
			<td colspan="3" height="5" ></td>
		</tr>
		</table></td>
					</tr>
				</table>
				<!-- Form fields end   -->
		</td>
		</tr>		
		<tr>
			<td height="10"></td>
		</tr>
	<input type="hidden" name="selCoD" value="<?=$selCoD?>">
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
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
