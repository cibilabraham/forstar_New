<?php
	require("include/include.php");
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
//----------------------------------------------------------
	
	$selection = "?pageNo=".$p["pageNo"];
	
	# select record between selected date
	if ($p["selectFrom"]=="" && $p["selectTill"]=="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	}
	
	# TI/ PI / SI
	if ($g["invoiceType"]!="") $invoiceType	= $g["invoiceType"];
	else if ($p["invoiceType"]!="") $invoiceType = $p["invoiceType"];
	
	# Invoice Year 
	$invYear = date("Y", strtotime(date("Y-m-d")));
	$invType = ($invoiceType=='TI')?"T":"";	

	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);

	# Cancel Invoice
	if ($p["cmdCancelInvoice"]!="") {

		$rowCount	= $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$invoiceId 	= $p["invoiceId_".$i];
			$cancelled 	= $p["cancelled_".$i];
			$cnclReason	= $p["cnclReason_".$i];
			# Update SO
			if ($invoiceId!="" && $cancelled=="" && $invoiceType!="TI") {
				$invoiceCancelled = $invoiceVerificationObj->cancelInvoice($invoiceId, $cnclReason);	
			}
			# insert cancelled TAX Invoice
			if ($invoiceId!="" && $cancelled=="" && $invoiceType=="TI") {
				$invoiceCancelled = $invoiceVerificationObj->InsInvoiceRec($invoiceId, $invYear, $invType, $userId);	
			}
		}
		if ($invoiceCancelled) {
			$sessObj->createSession("displayMsg",$msgSuccCancelInvoice);
		} else {
			$err	=	$msgFailAddCancelInvoice;
		}
		$invoiceCancelled	=	false;
	}

	# Close Invoice
	if ($p["cmdCloseInvoice"]!="") {

		$rowCount	= $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$invoiceId 	= $p["invoiceId_".$i];
			$cancelled 	= $p["cancelled_".$i];
			$cnclReason	= $p["cnclReason_".$i];			

			if ($invoiceId!="" && $cancelled=="") {
				$invoiceClosed = $invoiceVerificationObj->closeInvoice($invoiceId, $cnclReason);	
			}
		}
		if ($invoiceClosed) {
			$sessObj->createSession("displayMsg",$msgSuccCloseInvoice);
		} else {
			$err	=	$msgFailAddCloseInvoice;
		}

		$invoiceClosed	=	false;
	}

	# Change Status
	if ($p["cmdChangeInvoice"]!="") {

		$rowCount	= $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$invoiceId 	= $p["invoiceId_".$i];
			$cancelled 	= $p["cancelled_".$i];
			$cnclReason	= $p["cnclReason_".$i];	
			$cancelledInvoiceId	= $p["cancelledInvoiceId_".$i];	

			# Update SO
			if ($invoiceId!="" && $cancelled!="" && $invoiceType!="TI") {
				$invoiceStatusChanged = $invoiceVerificationObj->changeInvoiceStatus($invoiceId);	
			}
			# Del Canceled invoice
			if ($invoiceId!="" && $cancelled!="" && $invoiceType=="TI") {
				$invoiceStatusChanged = $invoiceVerificationObj->delCancelledInvoice($cancelledInvoiceId);	
			}
		}
		if ($invoiceStatusChanged) {
			$sessObj->createSession("displayMsg", $msgSuccChangeInvoice);			
		} else {
			$err	=	$msgFailAddChangeInvoice;
		}

		$invoiceStatusChanged	=	false;
	}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo-1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#Select the records based on date
	if (($p["cmdSearch"]!="" || $p["cmdCancelInvoice"]!="") || ($dateFrom!="" && $dateTill!="") ) {

		if ($invoiceType!="TI") {
			$invoiceRecords	= $invoiceVerificationObj->getPaginatedInvoiceRecords($fromDate, $tillDate, $offset, $limit, $invoiceType);
			# Get all Missing Records
			$fetchAllInvoiceRecords = $invoiceVerificationObj->fetchAllInvoiceRecords($fromDate, $tillDate, $invoiceType);
		} else if ($invoiceType=="TI") {
			# Taxable Invoice Records
			$invoiceRecords = $invoiceVerificationObj->getPaginatedMissingInvoiceRecords($fromDate, $tillDate, $offset, $limit, $invoiceType) ;
			# Get all Missing Records
			$fetchAllInvoiceRecords = $invoiceVerificationObj->fetchAllMissingRecs();
		}
		
		/*
		echo "<pre>";
		echo "Res=================";
		print_r($fetchAllInvoiceRecords);		
		echo "</pre>";
		*/

		$invoiceRecordSize = sizeof($invoiceRecords);
			
	}

	
	## -------------- Pagination Settings II -------------------
	$numrows	= sizeof($fetchAllInvoiceRecords);
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	$ON_LOAD_PRINT_JS	= "libjs/InvoiceVerification.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmInvoiceVerification" action="InvoiceVerification.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<tr>
			<td height="30" align="center" class="err1" ></td>
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
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; Invoice verification</td>
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
                        <td colspan="4" align="center"></td>
                        <?} ?>
                      </tr>
                      <input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
                      <tr>
                        <td colspan="3" nowrap height="5"></td>
                        </tr>
                      <tr>
                        <td class="fieldName" nowrap >&nbsp;</td>
                        <td colspan="2" align="center"><table width="250">
                                  <tr> 
                                    <td class="fieldName" nowrap="true">&nbsp;From:</td>
                                    <td nowrap="true"> 
                                       <input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>"></td>
					<td class="fieldName" nowrap="true">&nbsp;To:</td>
					<td nowrap="true">
                                        <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>">
					</td>
					<td class="fieldName" nowrap="true">&nbsp;Invoice:</td>
					<td nowrap="true">
						<select name="invoiceType" id="invoiceType">
							<option value="TI" <? if ($invoiceType=='TI') echo "Selected";?> >Taxable</option>
							<option value="PI" <? if ($invoiceType=='PI') echo "Selected";?> >Proforma</option>
							<option value="SI" <? if ($invoiceType=='SI') echo "Selected";?> >Sample</option>
							</select>
					</td>
					<td>
						<input type="submit" name="cmdSearch" value=" Search" class="button">
					</td>
                                  </tr>
                                </table></td>
                        </tr>
                      <tr> 
                        <td class="fieldName" nowrap >&nbsp;</td>
                        <td colspan="2" align="center"><table width="250" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td class="fieldName"></td>
                                    <td></td>
                                  </tr>
</table></td>
                        </tr>
			<tr><TD height="10"></TD></tr>
                      <? 
			 if (sizeof($invoiceRecords)>0) {
				 $i = 0;
		      ?>

			<tr><TD nowrap colspan="3" align="center">
				<? if ($isAdmin!="" || $edit!="" || $reEdit!="") { ?>
					<input type="submit" value=" Cancel Invoice " class="button"  name="cmdCancelInvoice" onClick="return validateCancelInvoice(this.form,'invoiceId_',<?=$invoiceRecordSize;?>);" style="width:100px;"> 
					<?php if ($invoiceType!="TI") {?>
					&nbsp;&nbsp;
					<input type="submit" value=" Close Invoice " class="button"  name="cmdCloseInvoice" onClick="return validateCloseInvoice(this.form,'invoiceId_',<?=$invoiceRecordSize;?>);" style="width:100px;">
					<?php }?>
				<? }?>
				<? if ($isAdmin!="") { ?>
					&nbsp;&nbsp;
					<input type="submit" value=" Change Invoice Status " class="button"  name="cmdChangeInvoice" onClick="return validateChangeInvoice(this.form,'invoiceId_',<?=$invoiceRecordSize;?>);" style="width:150px;">
				<? }?>
			</TD></tr>
			<tr><TD height="10"></TD></tr>
			<? if($err!="" ){?>
			<tr>
			<td height="25" align="center" class="err1" colspan="3"><? if($err!="" ){?><?=$err;?><?}?></td>
			</tr>
			<?}?>
	<?php
		if ($invoiceType!='TI') {
	?>
                      <tr>
                        <td colspan="4" align="center">
		<table width="60%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
<? if($maxpage>1){?>
<tr bgcolor="#FFFFFF">
	<td colspan="4" align="right" style="padding-right:10px;">
	<div align="right">
	<?php 				 			  
		 $nav  = '';
		for($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
				$nav.= " <a href=\"InvoiceVerification.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&invoiceType=$invoiceType\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"InvoiceVerification.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&invoiceType=$invoiceType\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"InvoiceVerification.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&invoiceType=$invoiceType\"  class=\"link1\">>></a> ";
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
              <tr bgcolor="#f2f2f2" align="center">
                <th nowrap="nowrap" class="listing-head" width="20">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'invoiceId_'); " class="chkBox"> 
		</th>
                <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;">Invoice No</th>
		<th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;">Reason</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Status</th>
		  </tr>
              <?php
		foreach ($invoiceRecords as $ir) {
			$i++;		
			$invoiceId	= $ir[0];	
			$soNo	 	= $ir[1];
			$status		= $ir[5];
			$cnclReason	= $ir[6];
			$displayStatus = "";
			$cancelled = false;
			if ($status=='CN')	$displayStatus = "Cancelled";
			else if ($status=='CL') $displayStatus = "Closed";
			
			$displayStyle = "";
			if ($status=='CN' || $status=='CL')  {
				$cancelled = true;
				$displayStyle = " style='border:none;' readonly ";
			}
			$proformaNo	= $ir[3];
			$sampleNo	= $ir[4];
			$soInvoiceType	= $ir[7];
			$invoiceNo = "";
			if ($soNo!=0) $invoiceNo=$soNo;
			else if ($soInvoiceType=='T') $invoiceNo = "P$proformaNo";
			else if ($soInvoiceType=='S') $invoiceNo = "S$sampleNo";
		?>
              <tr bgcolor="#FFFFFF">
		 <td class="listing-item" nowrap height='25' width="20" align="center">
			<input type="checkbox" name="invoiceId_<?=$i;?>" id="invoiceId_<?=$i;?>" value="<?=$invoiceId;?>" class="chkBox">
			<input type="hidden" name="cancelled_<?=$i;?>" id="cancelled_<?=$i;?>" value="<?=$cancelled;?>" ></td>
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$invoiceNo?></td>
		 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="center">
			<input type="text" name="cnclReason_<?=$i?>" id="cnclReason_<?=$i?>" value="<?=$cnclReason?>" <?=$displayStyle?>>
		</td>
		 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="center"><?=$displayStatus?></td>
 		</tr>
		<?
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
			<td align="right" style="padding-right:10px" colspan="4"><div align="right">
			<?php 				 			  
			$nav  = '';
			for ($page=1; $page<=$maxpage; $page++) {
				if ($page==$pageNo) {
      					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   				} else {
      					$nav.= " <a href=\"InvoiceVerification.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&invoiceType=$invoiceType\" class=\"link1\">$page</a> ";
				}
			}
			if ($pageNo > 1) {
   				$page  = $pageNo - 1;
   				$prev  = " <a href=\"InvoiceVerification.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&invoiceType=$invoiceType\"  class=\"link1\"><<</a> ";
	 		} else {
   				$prev  = '&nbsp;'; // we're on page one, don't print previous link
   				$first = '&nbsp;'; // nor the first page link
			}
			if ($pageNo < $maxpage) {
   				$page = $pageNo + 1;
   				$next = " <a href=\"InvoiceVerification.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&invoiceType=$invoiceType\"  class=\"link1\">>></a> ";
	 		} else {
   				$next = '&nbsp;'; // we're on the last page, don't print next link
   				$last = '&nbsp;'; // nor the last page link
			}
			// print the navigation link
			$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
			echo $first . $prev . $nav . $next . $last . $summary; 
	  	?>
	  </div></td></tr>
	<? }?>
      </table></td>
                        </tr>
	<?php
		} // PI/ SI Ends here
	?>
	<?php
		# For Taxable invoice
		if ($invoiceType=='TI') {
	?>
                      <tr>
                        <td colspan="4" align="center">
		<table width="60%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
<? if($maxpage>1){?>
<tr bgcolor="#FFFFFF">
	<td colspan="4" align="right" style="padding-right:10px;">
	<div align="right">
	<?php 				 			  
		 $nav  = '';
		for($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
				$nav.= " <a href=\"InvoiceVerification.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&invoiceType=$invoiceType\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"InvoiceVerification.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&invoiceType=$invoiceType\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"InvoiceVerification.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&invoiceType=$invoiceType\"  class=\"link1\">>></a> ";
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
              <tr bgcolor="#f2f2f2" align="center">
                <th nowrap="nowrap" class="listing-head" width="20">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'invoiceId_'); " class="chkBox"> 
		</th>
                <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;">Invoice No</th>	
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Status</th>
		  </tr>
              <?php
		
		foreach ($invoiceRecords as $key=>$invNo) {
			$i++;
			$cancelled = false;
			# cancelled Invoice Id
			$cancelledInvoiceId = $invoiceVerificationObj->getCancelledInvoice($invNo, $invYear, $invType);
			$displayStatus = "";
			if ($cancelledInvoiceId) {
				$displayStatus="Cancelled";
				$cancelled = true;
			}
		?>
              <tr bgcolor="#FFFFFF">
		 <td class="listing-item" nowrap height='25' width="20" align="center">
			<input type="checkbox" name="invoiceId_<?=$i;?>" id="invoiceId_<?=$i;?>" value="<?=$invNo;?>" class="chkBox">
			<input type="hidden" name="cancelled_<?=$i;?>" id="cancelled_<?=$i;?>" value="<?=$cancelled;?>" >
			<input type="hidden" name="cancelledInvoiceId_<?=$i;?>" id="cancelledInvoiceId_<?=$i;?>" value="<?=$cancelledInvoiceId;?>" >
		</td>
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$invNo?></td>		
		 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="center"><?=$displayStatus?></td>
 		</tr>
		<?php
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
			<td align="right" style="padding-right:10px" colspan="4"><div align="right">
			<?php 				 			  
			$nav  = '';
			for ($page=1; $page<=$maxpage; $page++) {
				if ($page==$pageNo) {
      					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   				} else {
      					$nav.= " <a href=\"InvoiceVerification.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&invoiceType=$invoiceType\" class=\"link1\">$page</a> ";
				}
			}
			if ($pageNo > 1) {
   				$page  = $pageNo - 1;
   				$prev  = " <a href=\"InvoiceVerification.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&invoiceType=$invoiceType\"  class=\"link1\"><<</a> ";
	 		} else {
   				$prev  = '&nbsp;'; // we're on page one, don't print previous link
   				$first = '&nbsp;'; // nor the first page link
			}
			if ($pageNo < $maxpage) {
   				$page = $pageNo + 1;
   				$next = " <a href=\"InvoiceVerification.php?pageNo=$page&selectFrom=$dateFrom&selectTill=$dateTill&invoiceType=$invoiceType\"  class=\"link1\">>></a> ";
	 		} else {
   				$next = '&nbsp;'; // we're on the last page, don't print next link
   				$last = '&nbsp;'; // nor the last page link
			}
			// print the navigation link
			$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
			echo $first . $prev . $nav . $next . $last . $summary; 
	  	?>
	  </div></td></tr>
	<? }?>
      </table></td>
                        </tr>
	<?php
		} // PI/ SI Ends here
	?>
		<tr><TD height="10"></TD></tr>
		<tr><TD nowrap colspan="3" align="center">
			<? if ($isAdmin!="" || $edit!="" || $reEdit!="") { ?>
				<input type="submit" value=" Cancel Invoice " class="button"  name="cmdCancelInvoice" onClick="return validateCancelInvoice(this.form,'invoiceId_',<?=$invoiceRecordSize;?>);" style="width:100px;"> 
				<?php if ($invoiceType!="TI") {?>
				&nbsp;&nbsp;<input type="submit" value=" Close Invoice " class="button"  name="cmdCloseInvoice" onClick="return validateCloseInvoice(this.form,'invoiceId_',<?=$invoiceRecordSize;?>);" style="width:100px;">
				<?php }?>
				<? }?>
				<? if ($isAdmin!="") { ?>
					&nbsp;&nbsp;
					<input type="submit" value=" Change Invoice Status " class="button"  name="cmdChangeInvoice" onClick="return validateChangeInvoice(this.form,'invoiceId_',<?=$invoiceRecordSize;?>);" style="width:150px;">
				<? }?>
		</TD></tr>
		<? } else if($dateFrom!="" && $dateTill!="") {
		?>
		<tr bgcolor="white">
			<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
		</tr>
		<?
			}
		?>      <tr> 
                        <td colspan="4" align="center" class="err1"><? if(sizeof($invoiceRecords)<=0 && $selectSupplier!=""){ echo $msgNoSettlement;}?></td>
                        </tr>
		
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center"></td>
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
	
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
