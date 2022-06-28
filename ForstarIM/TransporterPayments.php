<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$redirectLocation = "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"]."&transporterFilter=".$p["transporterFilter"];

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
		header ("Location: ErrorPage.php");
		die();	
	}		
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------
	//Coming from Transporter Setlement Summary
	$paidTransporter = $g["transporter"];
	$paidAmount = $g["totalPayingAmount"];
	if ($paidTransporter!="") {
		$paidRecords = $transporterPaymentsObj->getPaidRecords($paidTransporter);
		$advanceAmount = 0;
		$alreadyPaidAmount = 0;
		foreach($paidRecords as $pr){
			$amountPaid = $pr[2];
			$paidMode	=	$pr[3];
			if ($paidMode=='A') {
				$advanceAmount += $amountPaid;
			} else {
				$alreadyPaidAmount +=$amountPaid;
			}
	}
		$balanceAmount = $advanceAmount - ($alreadyPaidAmount+$paidAmount);
	}

	# Add New Supplier Payments	
	if ($p["cmdAddNew"]!="" || $paidTransporter!="") $addMode = true;	
	if ($p["cmdCancel"]!="") $addMode = false;

	# Add Supplier Payments
	if ($p["cmdAdd"]!="") {
		$transporter		= $p["transporter"];
		$chequeNo		= $p["chequeNo"];
		$amount			= $p["amount"];
		$paymentMode		= ($p["paidTransporter"]=="")?A:D; //A-advance - D-direct payment

		$paymentDate	= mysqlDateFormat($p["paymentDate"]);
		$bankName	= addSlash(trim($p["bankName"]));

		if ($transporter!="" && $amount!="" ) {
			$transporterPaymentsRecIns	=	$transporterPaymentsObj->addTransporterPayments($transporter, $chequeNo, $amount, $paymentMode, $userId, $paymentDate, $bankName);
			if ($transporterPaymentsRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddTransporterPayment);
				$sessObj->createSession("nextPage",$url_afterAddTransporterPayment.$redirectLocation);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddTransporterPayment;
			}
			$transporterPaymentsRecIns	=	false;
		}
		$addMode=false;
	}

	# Edit upplier Payments
	if ($p["editId"]!="") {	
		$editMode		=	true;		
		$editId			=	$p["editId"];
		$transporterPaymentsRec	=	$transporterPaymentsObj->find($editId);

		$transporterPaymentsId	=	$transporterPaymentsRec[0];
		$paidTransporterId	=	$transporterPaymentsRec[1];
		$cheque			=	$transporterPaymentsRec[2];
		$paidAmount		=	$transporterPaymentsRec[3];		
		$enteredDate		=	dateFormat($transporterPaymentsRec[4]);		
		$bankName	= stripSlash($transporterPaymentsRec[5]);
	}	
	
	if ($p["cmdSaveChange"]!="") {	
		$transporterPaymentsId		=	$p["hidSupplierPaymentsId"];
		$transporter			=	$p["transporter"];
		$chequeNo			=	$p["chequeNo"];
		$amount				=	$p["amount"];
		$paymentDate	= mysqlDateFormat($p["paymentDate"]);
		$bankName	= addSlash(trim($p["bankName"]));

		if ( $transporterPaymentsId!="" && $transporter!="" && $amount!="" ){		
			$transporterPaymentsRecUptd	=	$transporterPaymentsObj->updateTansporterPayments($transporterPaymentsId, $transporter, $chequeNo, $amount, $paymentDate, $bankName);		
		}
		if ($transporterPaymentsRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateTransporterPayment);
			$sessObj->createSession("nextPage",$url_afterUpdateTransporterPayment.$redirectLocation);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateTransporterPayment;
		}
		$transporterPaymentsRecUptd = false;
	}


	# Delete 	
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$transporterPaymentsId	=	$p["delId_".$i];
			if ($transporterPaymentsId!="" ) {
				$transporterPaymentsRecDel =	$transporterPaymentsObj->deleteTransporterPayments($transporterPaymentsId);
			}
		}
		if ($transporterPaymentsRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelTransporterPayment);
			$sessObj->createSession("nextPage",$url_afterDelTransporterPayment.$redirectLocation);
		} else {
			$errDel		=	$msg_failDelTransporterPayment;
		}
	}
		
	## -------------- Pagination Settings I ------------------
	if ( $p["pageNo"] != "" ) $pageNo=$p["pageNo"];
	else if ( $g["pageNo"] != "" )	$pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Records	
	# select records between selected date
	if ($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
		$transporterFilterId = $g["transporterFilter"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
		$transporterFilterId = $p["transporterFilter"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
		$transporterFilterId = "";
	}
	
	# Resettting offset values
	if ($p["hidTransporterFilterId"]!=$p["transporterFilter"]) {		
		$offset = 0;
		$pageNo = 1;		
	}

	
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$transporterPaymentsRecords = $transporterPaymentsObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit, $transporterFilterId);
		$numrows	=  sizeof($transporterPaymentsObj->fetchAllRecords($fromDate, $tillDate, $transporterFilterId));
	}		
	
		$transporterPaymentsRecordsSize		=	sizeof($transporterPaymentsRecords);
	
	## -------------- Pagination Settings II -------------------
		$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	

	
	#List all Main Transporters
	//$transporterRecords	=	$transporterMasterObj->fetchAllRecords();
	$transporterRecords	=	$transporterMasterObj->fetchAllRecordsActiveTransporter();
	# Display heading
	if ($editMode)	$heading = $label_editTransporterPayment;
	else		$heading = $label_addTransporterPayment;
	
	$ON_LOAD_PRINT_JS	= "libjs/TransporterPayments.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
	<form name="frmTransporterPayments" action="TransporterPayments.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%">
		<tr>
		  <td height="30" align="center" class="err1"><? if($balanceAmount>0){?>Advance Balance = <?=$balanceAmount?><? }?></td>
	  </tr>
		<tr>
			<td height="30" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?
			if( $editMode || $addMode )
			{
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
									<td colspan="2"  align="center">
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('Fish2GradeMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateTransporterPayments(document.frmTransporterPayments);">												</td>
												
												<?} else{?>

												<td align="center" colspan="2">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Fish2GradeMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateTransporterPayments(document.frmTransporterPayments);">												</td>
											
												<?} ?>
											</tr>
		<input type="hidden" name="hidSupplierPaymentsId" value="<?=$transporterPaymentsId;?>">
		<tr><TD height="10"></TD></tr>
		<tr>
		<TD colspan="2" align="center">
		<table>
		<tr>
			<TD valign="top">
			<fieldset style="padding:5 5 5 5px;">
			<table>
				<tr>
				<td class="fieldName" nowrap="true">*Date of Payment:</td>
				<td>
					<input name="paymentDate" type="text" id="paymentDate" size="9" value="<? if($editMode==true) { echo $enteredDate; } else { echo date("d/m/Y");}?>" autocomplete="off" />
				</td>
				</tr>
				<tr>
				    <td class="fieldName">Chq.No:</td>
			            <td>
					<input name="chequeNo" type="text" id="chequeNo" size="20" value="<?=$cheque?>" autocomplete="off">
				     </td>
				</tr>
				<tr>
					    <td class="fieldName" nowrap="true">Issuing Bank:</td>
					    <td>
						<input type="text" name="bankName" id="bankName" size="24" value="<?=$bankName?>" autocomplete="off">
					    </td>
				</tr>
			</table>
			</fieldset>
			</TD>
			<TD valign="top">&nbsp;</TD>
			<TD valign="top">
			<fieldset style="padding:5 5 5 5px;">
			<table>
				<TR>
				  <td class="fieldName">*Transporter:</td>
				<td>				
                                 <select name="transporter">
                                              <option value="">--select--</option>
                                                <?php
							foreach($transporterRecords as $tr)
							{
								$i++;
								$transporterId	=	$tr[0];
								$transporterName	=	stripSlash($tr[2]);
								$selected	=	"";
								if ($transporterId == $paidTransporterId || $paidTransporter==$transporterId) {
									$selected	=	"selected";
								}
						?>
                              <option value="<?=$transporterId?>" <?=$selected?>><?=$transporterName?>
                                                  </option>
                                                  <? } ?>
                                                </select></td>	
				</TR>
				<tr>
				 <td class="fieldName" nowrap="true">*Amount (Rs.):</td>
				 <td>
					<input name="amount" type="text" id="amount" size="9" value="<?=$paidAmount?>" style="text-align:right;" autocomplete="off">
				 </td>
				</tr>
			</table>
			</fieldset>
			</TD>
		</tr>
		</table>
		</TD>
	</tr>							
											
											
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Fish2GradeMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateTransporterPayments(document.frmTransporterPayments);">												</td>
												
												<?} else{?>

												<td align="center" colspan="2">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Fish2GradeMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateTransporterPayments(document.frmTransporterPayments);">												</td>
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
		<tr>
			<td height="10" ></td>
		</tr>
		<?
			}
			
			# Listing Fish-Grade Starts
		?>
		
		<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Transporter Payments </td>
								
<td background="images/heading_bg.gif" nowrap="true" align="right">
	<table cellpadding="0" cellspacing="0">
									  <tr>
					<td nowrap="nowrap">
					<table cellpadding="0" cellspacing="0">
                      			<tr>
					  	<td class="listing-item">&nbsp;From:&nbsp;</td>
                                    		<td nowrap="nowrap"> 
                            		<?php 
						if ($dateFrom=="") $dateFrom=date("d/m/Y");
					?>
                            			<input type="text" id="selectFrom" name="selectFrom" size="9" value="<?=$dateFrom?>">
						</td>
					    <td class="listing-item">&nbsp;</td>
				            <td class="listing-item">&nbsp;Till:&nbsp;</td>
                                    <td> 
                                      <?php 
					   if($dateTill=="") $dateTill=date("d/m/Y");
				      ?>
                                      		<input type="text" id="selectTill" name="selectTill" size="9"  value="<?=$dateTill?>">
					</td>
						<td class="listing-item" nowrap="true">&nbsp;Transporter:&nbsp;</td>			
						<td>
							<select name="transporterFilter" id="transporterFilter" style="width:100px;">
							<option value="">-- Select All --</option>
							<? 
							if (sizeof($transporterRecords)>0) {
								foreach($transporterRecords as $tr)
								{							$fTransporterId	=	$tr[0];
									$fTrptrName =	stripSlash($tr[2]);
									$selected	= ($fTransporterId==$transporterFilterId)?"selected":"";
						?>
							<option value="<?=$fTransporterId?>" <?=$selected?>><?=$fTrptrName?></option>
							<? 
								} 
							}
							?>
							</select>
						</td>		
					   <td class="listing-item">&nbsp;</td>
					        <td>
							<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search">
						</td>
                            <td class="listing-item" nowrap >&nbsp;</td>
                          </tr>
                    </table>
				</td>
			</tr>
			</table>
	</td>
							</tr>
							<tr>
								<td colspan="3" height="10" >								</td>
							</tr>
							<tr>	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$transporterPaymentsRecordsSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintTransporterPayments.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>&transporterFilter=<?=$transporterFilterId?>',700,600);"><? }?></td>
										</tr>
									</table>								</td>
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
								<td colspan="2" style="padding-left:5px; padding-right:5px;">
									<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
									<?
									if( sizeof($transporterPaymentsRecords) > 0 )
											{
												$i	=	0;
											?>
										
										 <? if($maxpage>1){?>
											<tr bgcolor="#FFFFFF"><td colspan="7" style="padding-right:10px"><div align="right">
				  <?php 				 			  
				 $nav  = '';
		for($page=1; $page<=$maxpage; $page++)
			{
				if ($page==$pageNo)
   				{
      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   				}
   				else
   				{
      	$nav.= " <a href=\"TransporterPayments.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&transporterFilter=$transporterFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"TransporterPayments.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&transporterFilter=$transporterFilterId\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"TransporterPayments.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&transporterFilter=$transporterFilterId\"  class=\"link1\">>></a> ";
	 	}
		else
		{
   		$next = '&nbsp;'; // we're on the last page, don't print next link
   		$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr><? }?>
										<tr  bgcolor="#f2f2f2"  align="center">
											<td width="20" height="1"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
											<td nowrap class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>
											<td nowrap class="listing-head" style="padding-left:10px; padding-right:10px;">Transporter</td>
											<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Cheque No</td>
	<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Issuing Bank</td>	
											<td align="right" class="listing-head" style="padding-left:10px; padding-right:10px;">Amount</td>
											<? if($edit==true){?>
											<td class="listing-head" width="65"></td>
											<? }?>
										</tr>
	<?
		$totalAmtPaid = 0;
		foreach($transporterPaymentsRecords as $spr)	{					
			$i++;
			$paymentId	=	$spr[0];
			$selPaymentDate	= dateFormat($spr[4]);
			$chequeNo		=	$spr[2];
			$amountPaid		=	$spr[3];
			$transporterName	=	$spr[6];
			$issuingBankName 	= $spr[7];
			$totalAmtPaid += $amountPaid;
	?>
										
										<tr  bgcolor="WHITE"  >

											<td width="20" height="25" class="listing-item"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$paymentId;?>" class="chkBox"></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selPaymentDate?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$transporterName;?></td>
											<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$chequeNo?></td>
	<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$issuingBankName?></td>
											<td class="listing-item"  align="right" style="padding-left:10px; padding-right:10px;"><?=$amountPaid?></td>
											<? if($edit==true){?>
											<td class="listing-item" width="65" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$paymentId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='TransporterPayments.php';"  ></td>
											<? }?>
										</tr>
										<?
												}
										?>
	<tr bgcolor="WHITE">
		<TD colspan="5" class="listing-head" align="right">Total:&nbsp;</TD>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><strong><?=number_format($totalAmtPaid,2,'.',',')?></strong></td>
		<td class="listing-item">&nbsp;</td>
	</tr>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
								 <? if($maxpage>1){?>
											<tr bgcolor="#FFFFFF"><td colspan="7" style="padding-right:10px"><div align="right">
				  <?php 				 			  
				 $nav  = '';
		for($page=1; $page<=$maxpage; $page++)
			{
				if ($page==$pageNo)
   				{
      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   				}
   				else
   				{
      	$nav.= " <a href=\"TransporterPayments.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&transporterFilter=$transporterFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"TransporterPayments.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&transporterFilter=$transporterFilterId\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"TransporterPayments.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&transporterFilter=$transporterFilterId\"  class=\"link1\">>></a> ";
	 	}
		else
		{
   		$next = '&nbsp;'; // we're on the last page, don't print next link
   		$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr><? }?>
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
		<input type="hidden" name="paidTransporter" value="<?=$paidTransporter?>">		
					</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
							<tr >	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$transporterPaymentsRecordsSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintTransporterPayments.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>&transporterFilter=<?=$transporterFilterId?>',700,600);"><? }?></td>
										</tr>
									</table>								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
						</table>					</td>
				</tr>
			</table>
			<!-- Form fields end   -->		</td>
	</tr>	

	<tr>
		<td height="10"><input type="hidden" name="hidTransporterFilterId" value="<?=$transporterFilterId?>"></td>
	</tr>	
	</table>
	
<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "paymentDate",         // ID of the input field
			eventName  : "click",	    // name of event
			button : "paymentDate", 
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
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
