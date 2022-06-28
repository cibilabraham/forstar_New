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
	if ($p["supplyFrom"]=="" && $p["supplyTill"]=="") {
		$dateFrom = $g["supplyFrom"];
		$dateTill = $g["supplyTill"];
	} else {
		$dateFrom = $p["supplyFrom"];
		$dateTill = $p["supplyTill"];
	}
	if ($g["billingCompany"]!="") $billingCompany	=	$g["billingCompany"];
	else if ($p["billingCompany"]!="") $billingCompany	=	$p["billingCompany"];

	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);

	# Cancel Challan
	if ($p["cmdCancelChallan"]!="") {

		$rowCount	= $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$challanNo = $p["challanNo_".$i];
			$cancelled = $p["cancelled_".$i];

			if ($challanNo!="" && $cancelled=="") {
				$challanNoCancelled = $challanVerificationObj->cancelChallan($challanNo, $billingCompany);	
			}
		}
		if ($challanNoCancelled) {
			$sessObj->createSession("displayMsg",$msgSuccCancelChallan);
			//$sessObj->createSession("nextPage",$url_afterDelBrand.$selCriteria);
		} else {
			$err	=	$msgFailAddCancelChallan;
		}
		$challanNoCancelled	=	false;
	}
	//
	# Change challan Status
	if ($p["cmdChangeChallan"]!="") {

		$rowCount	= $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$challanNo = $p["challanNo_".$i];
			$cancelled = $p["cancelled_".$i];
			$cancelledChallanId = $p["cancelledChallanId_".$i];

			if ($challanNo!="" && $cancelled!="") {
				$changeChallanNoStatus = $challanVerificationObj->changeChallanStatus($cancelledChallanId);	
			}
		}
		if ($changeChallanNoStatus) {
			$sessObj->createSession("displayMsg",$msgSuccChangeChallan);
			//$sessObj->createSession("nextPage",$url_afterDelBrand.$selCriteria);
		} else {
			$err	=	$msgFailAddChangeChallan;
		}
		$changeChallanNoStatus	= false;
	}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo-1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#Select the records based on date
	if (($p["cmdSearch"]!="" || $p["cmdCancelChallan"]!="") || ($dateFrom!="" && $dateTill!="") ) {
		$missingChallanRecords	= $challanVerificationObj->getPaginatedMissingRecords($fromDate, $tillDate, $offset, $limit, $billingCompany);
		$missingChallanRecordSize = sizeof($missingChallanRecords);
		// Get all Missing Records
		$fetchAllMissingRecords = $challanVerificationObj->getMissingRecords($fromDate, $tillDate, $billingCompany);	
	}

	# Get Billing Comapany  Records
	//$billingCompanyRecords = $billingCompanyObj->fetchAllRecords();
	$billingCompanyRecords = $billingCompanyObj->fetchAllRecordsActivebillingCompany();
	
	## -------------- Pagination Settings II -------------------
	$numrows	= sizeof($fetchAllMissingRecords);
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	$ON_LOAD_PRINT_JS	= "libjs/ChallanVerification.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmChallanVerification" action="ChallanVerification.php" method="Post">
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
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; Missing Challan verification</td>
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
                                       <input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>"></td>
					<td class="fieldName" nowrap="true">&nbsp;To:</td>
					<td nowrap="true">
                                        <input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>">
					</td>
					<td class="fieldName" nowrap="true">&nbsp;Billing Company:</td>
					<td nowrap="true">
						<select name="billingCompany" id="billingCompany">
						<!--<option value="">-- Select --</option>-->
						<?php
						foreach ($billingCompanyRecords as $bcr) {
							$billingCompanyId	= $bcr[0];
							$cName			= $bcr[1];
							$defaultChk		= $bcr[10];
							$alphaCode		= $bcr[8];
							$displayCName		= $bcr[9];
							$selected = "";
							if ($billingCompanyId==$billingCompany || ($billingCompany=="" && $defaultChk=='Y') ) $selected = "selected";
						?>
						<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$displayCName?></option>
						<?	
						}	
						?>
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
			 if (sizeof($missingChallanRecords)>0) {
				 $i = 0;
		      ?>

			<tr><TD nowrap colspan="3" align="center">
				<? if ($isAdmin!="" || $edit!="" || $reEdit!="") { ?>
				<input type="submit" value=" Cancel Challan No " class="button"  name="cmdCancelChallan" onClick="return confirmCancelChallan(this.form,'challanNo_',<?=$missingChallanRecordSize;?>);" style="width:130px;">
				<? }?>
				<? if ($isAdmin!="" || $reEdit) { ?>
					&nbsp;&nbsp;
					<input type="submit" value=" Change Challan Status " class="button"  name="cmdChangeChallan" onClick="return validateChangeChallan(this.form,'challanNo_',<?=$missingChallanRecordSize;?>);" style="width:160px;">
				<? }?>
			</TD></tr>
			<tr><TD height="10"></TD></tr>
			<? if($err!="" ){?>
			<tr>
			<td height="30" align="center" class="err1" colspan="3"><? if($err!="" ){?><?=$err;?><?}?></td>
			</tr>
			<?}?>
                      <tr>
                        <td colspan="4" align="center">
		<table width="60%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
<? if($maxpage>1){?>
<tr bgcolor="#FFFFFF">
	<td colspan="3" align="right" style="padding-right:10px;">
	<div align="right">
	<?php 				 			  
		 $nav  = '';
		for($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
				$nav.= " <a href=\"ChallanVerification.php?pageNo=$page&supplyFrom=$dateFrom&supplyTill=$dateTill&billingCompany=$billingCompany\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ChallanVerification.php?pageNo=$page&supplyFrom=$dateFrom&supplyTill=$dateTill&billingCompany=$billingCompany\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ChallanVerification.php?pageNo=$page&supplyFrom=$dateFrom&supplyTill=$dateTill&billingCompany=$billingCompany\"  class=\"link1\">>></a> ";
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
                <th nowrap="nowrap" class="listing-head" width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'challanNo_'); " class="chkBox"> </th>
                <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;">Challan No</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Status</th>
		  </tr>
              <?php
		$cancelledChallanId = "";
		foreach ($missingChallanRecords as $key=>$value) {
			$i++;			
			$missingChallan = $value;
			$displayStatus = "";
			$cancelled = false;
			// chkChallanCancelled($missingChallan, $billingCompany)
			
			/*
			if ($challanVerificationObj->checkCancelled($missingChallan, $billingCompany)) {
				$displayStatus="Cancelled";
				$cancelled = true;
			}
			*/
			# Chech Challan Cancelled
			list($challanCancelled, $cancelledChallanId) = $challanVerificationObj->chkChallanCancelled($missingChallan, $billingCompany);

			if ($challanCancelled) {
				$displayStatus="Cancelled";
				$cancelled = true;
			}
		?>
              <tr bgcolor="#FFFFFF">
		 <td class="listing-item" nowrap height='25' width="20" align="center">
			<input type="checkbox" name="challanNo_<?=$i;?>" id="challanNo_<?=$i;?>" value="<?=$missingChallan;?>" class="chkBox">
			<input type="hidden" name="cancelled_<?=$i;?>" id="cancelled_<?=$i;?>" value="<?=$cancelled;?>" >
			<input type="hidden" name="cancelledChallanId_<?=$i;?>" id="cancelledChallanId_<?=$i;?>" value="<?=$cancelledChallanId;?>" >
		</td>
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$missingChallan?></td>
		 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="center"><?=$displayStatus?></td>
 		</tr>
		<?
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
			<td align="right" style="padding-right:10px" colspan="3"><div align="right">
			<?php 				 			  
			$nav  = '';
			for ($page=1; $page<=$maxpage; $page++) {
				if ($page==$pageNo) {
      					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   				} else {
      					$nav.= " <a href=\"ChallanVerification.php?pageNo=$page&supplyFrom=$dateFrom&supplyTill=$dateTill&billingCompany=$billingCompany\" class=\"link1\">$page</a> ";
				}
			}
			if ($pageNo > 1) {
   				$page  = $pageNo - 1;
   				$prev  = " <a href=\"ChallanVerification.php?pageNo=$page&supplyFrom=$dateFrom&supplyTill=$dateTill&billingCompany=$billingCompany\"  class=\"link1\"><<</a> ";
	 		} else {
   				$prev  = '&nbsp;'; // we're on page one, don't print previous link
   				$first = '&nbsp;'; // nor the first page link
			}
			if ($pageNo < $maxpage) {
   				$page = $pageNo + 1;
   				$next = " <a href=\"ChallanVerification.php?pageNo=$page&supplyFrom=$dateFrom&supplyTill=$dateTill&billingCompany=$billingCompany\"  class=\"link1\">>></a> ";
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
		<tr><TD height="10"></TD></tr>
		<tr><TD nowrap colspan="3" align="center">
			<? if ($isAdmin!="" || $edit!="" || $reEdit!="") { ?>
			<input type="submit" value=" Cancel Challan No " class="button"  name="cmdCancelChallan" onClick="return confirmCancelChallan(this.form,'challanNo_',<?=$missingChallanRecordSize;?>);" style="width:130px;">
			<? }?>
			<? if ($isAdmin!="" || $reEdit) { ?>
					&nbsp;&nbsp;
					<input type="submit" value=" Change Challan Status " class="button"  name="cmdChangeChallan" onClick="return validateChangeChallan(this.form,'challanNo_',<?=$missingChallanRecordSize;?>);" style="width:160px;">
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
                        <td colspan="4" align="center" class="err1"><? if(sizeof($missingChallanRecords)<=0 && $selectSupplier!=""){ echo $msgNoSettlement;}?></td>
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
